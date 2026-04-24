<!-- TripDetail.vue -->
<template>
  <v-container class="detail" fluid>
    <Loader v-if="loading" text="Ielādē ceļojuma informāciju..." />
    <div v-else-if="error" class="state error">
      ⚠️ Ceļojums nav atrasts vai arī radās kļūda.
    </div>

    <div v-else-if="trip">
      <!-- Back button -->
      <v-btn class="back-btn" @click="$router.push('/services')">
        ← Atpakaļ uz ceļojumiem
      </v-btn>

      <!-- Hero -->
      <section class="hero">
        <div class="hero__top">
          <h1>{{ trip.nosaukums }}</h1>
          <FavoriteButton :trip-id="trip.celojuma_id" />
        </div>
        <p class="hero__dest">📍 {{ trip.galamerkis }}</p>
        <p class="hero__dates">
          🗓 {{ formatDate(trip.sakuma_datums) }} – {{ formatDate(trip.beigu_datums) }}
        </p>
        <p class="hero__owner" v-if="trip.lietotajs">
          👤 {{ trip.lietotajs.name }} {{ trip.lietotajs.uzvards }}
        </p>
        <div class="hero__budget">Budžets: € {{ trip.budzets }}</div>
      </section>

      <!-- Owner actions -->
      <section v-if="isOwner" class="owner-actions">
        <v-btn class="btn btn--edit" @click="$router.push('/services/' + trip.celojuma_id + '/edit')">
          ✏️ Rediģēt
        </v-btn>
        <v-btn class="btn btn--delete" :loading="deleting" @click="confirmDialog = true">
          🗑 Dzēst
        </v-btn>
      </section>

      <!-- Delete confirmation -->
      <v-dialog v-model="confirmDialog" max-width="420">
        <div class="dialog">
          <h3>Apstiprināt dzēšanu</h3>
          <p>Vai tiešām vēlies dzēst šo ceļojumu? Šī darbība ir neatgriezeniska.</p>
          <div class="dialog__actions">
            <v-btn class="btn" @click="confirmDialog = false">Atcelt</v-btn>
            <v-btn class="btn btn--delete" @click="doDelete">Dzēst</v-btn>
          </div>
        </div>
      </v-dialog>

      <!-- Summary stats -->
      <section class="summary">
        <div class="summary__card">
          <div class="summary__num">{{ trip.dienas_punkti?.length || 0 }}</div>
          <div class="summary__label">Dienas punkti</div>
        </div>
        <div class="summary__card">
          <div class="summary__num">{{ trip.rezervacijas?.length || 0 }}</div>
          <div class="summary__label">Rezervācijas</div>
        </div>
        <div class="summary__card">
          <div class="summary__num">€ {{ totalReservations }}</div>
          <div class="summary__label">Rezervāciju summa</div>
        </div>
        <div class="summary__card">
          <div class="summary__num">€ {{ totalExpenses }}</div>
          <div class="summary__label">Izdevumi kopā</div>
        </div>
        <div class="summary__card">
          <div class="summary__num">€ {{ remaining }}</div>
          <div class="summary__label">Budžeta atlikums</div>
        </div>
      </section>

      <!-- Budget progress bar -->
      <section class="budget-bar">
        <div class="budget-bar__header">
          <span>Budžeta izlietojums</span>
          <span class="budget-bar__percent" :class="budgetClass">
            {{ budgetPercent }}%
          </span>
        </div>
        <div class="budget-bar__track">
          <div
            class="budget-bar__fill"
            :class="budgetClass"
            :style="{ width: Math.min(budgetPercent, 100) + '%' }"
          ></div>
        </div>
        <div class="budget-bar__info">
          Iztērēts: € {{ totalSpent }} / € {{ trip.budzets }}
          <span v-if="remaining >= 0">(atlikums: € {{ remaining }})</span>
          <span v-else class="over">(pārsniegts par € {{ Math.abs(remaining).toFixed(2) }})</span>
        </div>
      </section>

      <!-- Day points -->
      <section class="block">
        <div class="block__header">
          <h2>🗺️ Maršruts</h2>
          <v-btn v-if="isOwner" class="add-btn" @click="openDayPointDialog()">
            + Pievienot
          </v-btn>
        </div>
        <div v-if="!trip.dienas_punkti?.length" class="empty">Nav pievienots neviens punkts.</div>
        <div v-else class="list">
          <div class="item" v-for="p in trip.dienas_punkti" :key="p.punkts_id">
            <div class="item__date">{{ formatDate(p.datums) }}</div>
            <div class="item__main">
              <div class="item__title" v-if="p.vieta">
                📍 {{ p.vieta.nosaukums }}
                <span class="item__type" v-if="p.vieta.tips">({{ p.vieta.tips }})</span>
              </div>
              <div class="item__sub" v-if="p.vieta">{{ p.vieta.adrese }}</div>
              <div class="item__desc">{{ p.apraksts }}</div>
            </div>
            <div v-if="isOwner" class="item__actions">
              <v-btn size="small" class="row-btn" @click="openDayPointDialog(p)">✏️</v-btn>
              <v-btn size="small" class="row-btn row-btn--del" @click="deleteDayPoint(p)">🗑</v-btn>
            </div>
          </div>
        </div>
      </section>

      <!-- Day point dialog -->
      <v-dialog v-model="dayPointDialog" max-width="600">
        <div class="dialog">
          <h3>{{ editingDayPoint ? "Rediģēt punktu" : "Jauns punkts" }}</h3>

          <div class="tabs">
            <v-btn
              :class="['tab', { active: placeMode === 'existing' }]"
              @click="placeMode = 'existing'"
              size="small"
            >
              Izvēlēties esošu
            </v-btn>
            <v-btn
              :class="['tab', { active: placeMode === 'new' }]"
              @click="placeMode = 'new'"
              size="small"
            >
              Pievienot jaunu
            </v-btn>
          </div>

          <div v-if="placeMode === 'existing'">
            <v-autocomplete
              v-model="dayPointForm.vieta_id"
              :items="places"
              item-title="nosaukums"
              item-value="vieta_id"
              label="Vieta"
              variant="outlined"
              :error-messages="dayPointErrors.vieta_id"
              :loading="placesLoading"
            />
          </div>

          <div v-else class="new-place">
            <v-text-field
              v-model="newPlaceForm.nosaukums"
              label="Vietas nosaukums *"
              variant="outlined"
              :error-messages="newPlaceErrors.nosaukums"
              maxlength="100"
            />
            <v-text-field
              v-model="newPlaceForm.adrese"
              label="Adrese"
              variant="outlined"
              maxlength="150"
            />
            <v-text-field
              v-model="newPlaceForm.tips"
              label="Tips (piem., Pludmale, Viesnīca)"
              variant="outlined"
              maxlength="50"
            />
            <v-text-field
              v-model="newPlaceForm.koordinatas"
              label="Koordinātas (piem., 56.9475, 24.1069)"
              variant="outlined"
              maxlength="100"
            />
          </div>

          <v-text-field
            v-model="dayPointForm.datums"
            label="Datums"
            type="date"
            variant="outlined"
            :error-messages="dayPointErrors.datums"
          />
          <v-textarea
            v-model="dayPointForm.apraksts"
            label="Apraksts"
            variant="outlined"
            rows="3"
            :error-messages="dayPointErrors.apraksts"
            maxlength="200"
            counter
          />

          <div v-if="dayPointServerError" class="error">{{ dayPointServerError }}</div>

          <div class="dialog__actions">
            <v-btn class="btn" @click="dayPointDialog = false">Atcelt</v-btn>
            <v-btn class="btn btn--primary" :loading="dayPointSaving" @click="saveDayPoint">
              Saglabāt
            </v-btn>
          </div>
        </div>
      </v-dialog>

      <!-- Reservations -->
      <section class="block">
        <div class="block__header">
          <h2>🛏️ Rezervācijas</h2>
          <v-btn v-if="isOwner" class="add-btn" @click="openReservationDialog()">
            + Pievienot
          </v-btn>
        </div>
        <div v-if="!trip.rezervacijas?.length" class="empty">Nav rezervāciju.</div>
        <table v-else class="table">
          <thead>
            <tr>
              <th>Tips</th>
              <th>Pakalpojums</th>
              <th>Cena</th>
              <th v-if="isOwner" style="width: 160px;">Darbības</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="r in trip.rezervacijas" :key="r.rezerv_num">
              <td>{{ r.tips }}</td>
              <td>{{ r.pakalpojuma_nosaukums }}</td>
              <td>€ {{ r.cena }}</td>
              <td v-if="isOwner">
                <v-btn size="small" class="row-btn" @click="openReservationDialog(r)">✏️</v-btn>
                <v-btn size="small" class="row-btn row-btn--del" @click="deleteReservation(r)">🗑</v-btn>
              </td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- Reservation dialog -->
      <v-dialog v-model="reservationDialog" max-width="500">
        <div class="dialog">
          <h3>{{ editingReservation ? "Rediģēt rezervāciju" : "Jauna rezervācija" }}</h3>

          <v-select
            v-model="reservationForm.tips"
            :items="['Aviobilete', 'Viesnīca', 'Cits']"
            label="Tips"
            variant="outlined"
            :error-messages="reservationErrors.tips"
          />
          <v-text-field
            v-model="reservationForm.pakalpojuma_nosaukums"
            label="Pakalpojuma nosaukums"
            variant="outlined"
            :error-messages="reservationErrors.pakalpojuma_nosaukums"
            maxlength="100"
          />
          <v-text-field
            v-model.number="reservationForm.cena"
            label="Cena (€)"
            type="number"
            min="0"
            step="0.01"
            variant="outlined"
            :error-messages="reservationErrors.cena"
          />

          <div v-if="reservationServerError" class="error">{{ reservationServerError }}</div>

          <div class="dialog__actions">
            <v-btn class="btn" @click="reservationDialog = false">Atcelt</v-btn>
            <v-btn class="btn btn--primary" :loading="reservationSaving" @click="saveReservation">
              Saglabāt
            </v-btn>
          </div>
        </div>
      </v-dialog>

      <!-- Expenses -->
      <section class="block">
        <div class="block__header">
          <h2>💰 Izdevumi</h2>
          <v-btn v-if="isOwner" class="add-btn" @click="openExpenseDialog()">
            + Pievienot
          </v-btn>
        </div>
        <div v-if="!trip.izdevumi?.length" class="empty">Nav izdevumu.</div>
        <table v-else class="table">
          <thead>
            <tr>
              <th>Datums</th>
              <th>Kategorija</th>
              <th>Summa</th>
              <th v-if="isOwner" style="width: 160px;">Darbības</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="e in trip.izdevumi" :key="e.izdevums_id">
              <td>{{ formatDate(e.datums) }}</td>
              <td>{{ e.kategorija }}</td>
              <td>€ {{ e.summa }}</td>
              <td v-if="isOwner">
                <v-btn size="small" class="row-btn" @click="openExpenseDialog(e)">✏️</v-btn>
                <v-btn size="small" class="row-btn row-btn--del" @click="deleteExpense(e)">🗑</v-btn>
              </td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- Expense dialog -->
      <v-dialog v-model="expenseDialog" max-width="500">
        <div class="dialog">
          <h3>{{ editingExpense ? "Rediģēt izdevumu" : "Jauns izdevums" }}</h3>

          <v-text-field
            v-model="expenseForm.kategorija"
            label="Kategorija (piem. Ēdiens, Transports)"
            variant="outlined"
            :error-messages="expenseErrors.kategorija"
            maxlength="50"
          />
          <v-text-field
            v-model="expenseForm.datums"
            label="Datums"
            type="date"
            variant="outlined"
            :error-messages="expenseErrors.datums"
          />
          <v-text-field
            v-model.number="expenseForm.summa"
            label="Summa (€)"
            type="number"
            min="0"
            step="0.01"
            variant="outlined"
            :error-messages="expenseErrors.summa"
          />

          <div v-if="expenseServerError" class="error">{{ expenseServerError }}</div>

          <div class="dialog__actions">
            <v-btn class="btn" @click="expenseDialog = false">Atcelt</v-btn>
            <v-btn class="btn btn--primary" :loading="expenseSaving" @click="saveExpense">
              Saglabāt
            </v-btn>
          </div>
        </div>
      </v-dialog>
    </div>
  </v-container>
</template>

<script>
import api from "@/api";
import Loader from "@/components/Loader.vue";
import FavoriteButton from "@/components/FavoriteButton.vue";
import { useAuthStore } from "@/stores/auth";

export default {
  components: { Loader, FavoriteButton },
  data() {
    return {
      trip: null,
      loading: true,
      error: null,
      deleting: false,
      confirmDialog: false,

      // Expense dialog state
      expenseDialog: false,
      editingExpense: null,
      expenseForm: { kategorija: "", datums: "", summa: null },
      expenseErrors: {},
      expenseServerError: "",
      expenseSaving: false,

      // Reservation dialog state
      reservationDialog: false,
      editingReservation: null,
      reservationForm: { tips: "", pakalpojuma_nosaukums: "", cena: null },
      reservationErrors: {},
      reservationServerError: "",
      reservationSaving: false,

      // Day point dialog state
      dayPointDialog: false,
      editingDayPoint: null,
      dayPointForm: { vieta_id: null, datums: "", apraksts: "" },
      dayPointErrors: {},
      dayPointServerError: "",
      dayPointSaving: false,

      placeMode: "existing",
      places: [],
      placesLoading: false,
      newPlaceForm: { nosaukums: "", adrese: "", tips: "", koordinatas: "" },
      newPlaceErrors: {},
    };
  },

  computed: {
    isOwner() {
      const auth = useAuthStore();
      return (
        this.trip &&
        auth.isLoggedIn &&
        auth.user?.id === this.trip.lietotajs_id
      );
    },
    totalReservations() {
      if (!this.trip?.rezervacijas) return 0;
      return this.trip.rezervacijas
        .reduce((sum, r) => sum + Number(r.cena || 0), 0)
        .toFixed(2);
    },
    totalExpenses() {
      if (!this.trip?.izdevumi) return 0;
      return this.trip.izdevumi
        .reduce((sum, e) => sum + Number(e.summa || 0), 0)
        .toFixed(2);
    },
    totalSpent() {
      return (Number(this.totalReservations) + Number(this.totalExpenses)).toFixed(2);
    },
    remaining() {
      return (Number(this.trip?.budzets || 0) - Number(this.totalSpent)).toFixed(2);
    },
    budgetPercent() {
      if (!this.trip?.budzets || this.trip.budzets == 0) return 0;
      return Math.round((this.totalSpent / this.trip.budzets) * 100);
    },
    budgetClass() {
      const p = this.budgetPercent;
      if (p >= 100) return "over";
      if (p >= 80) return "warn";
      return "ok";
    },
  },

  async mounted() {
    await this.loadTrip();
  },

  methods: {
    async loadTrip() {
      this.loading = true;
      try {
        const id = this.$route.params.id;
        this.trip = await api.getTrip(id);
      } catch (err) {
        this.error = err.message;
      } finally {
        this.loading = false;
      }
    },

    formatDate(d) {
      if (!d) return "";
      return new Date(d).toLocaleDateString("lv-LV");
    },

    async doDelete() {
      this.deleting = true;
      this.confirmDialog = false;
      try {
        await api.deleteTrip(this.trip.celojuma_id);
        this.$router.push("/services");
      } catch (err) {
        alert("Kļūda dzēšot: " + err.message);
      } finally {
        this.deleting = false;
      }
    },

    // --- Expenses ---
    openExpenseDialog(expense = null) {
      this.expenseErrors = {};
      this.expenseServerError = "";
      if (expense) {
        this.editingExpense = expense;
        this.expenseForm = {
          kategorija: expense.kategorija,
          datums: expense.datums,
          summa: Number(expense.summa),
        };
      } else {
        this.editingExpense = null;
        this.expenseForm = {
          kategorija: "",
          datums: new Date().toISOString().slice(0, 10),
          summa: null,
        };
      }
      this.expenseDialog = true;
    },

    validateExpense() {
      this.expenseErrors = {};
      if (!this.expenseForm.kategorija)
        this.expenseErrors.kategorija = "Kategorija ir obligāta";
      if (!this.expenseForm.datums)
        this.expenseErrors.datums = "Datums ir obligāts";
      if (this.expenseForm.summa === null || this.expenseForm.summa === "")
        this.expenseErrors.summa = "Summa ir obligāta";
      else if (this.expenseForm.summa < 0)
        this.expenseErrors.summa = "Summa nevar būt negatīva";
      return Object.keys(this.expenseErrors).length === 0;
    },

    async saveExpense() {
      this.expenseServerError = "";
      if (!this.validateExpense()) return;

      this.expenseSaving = true;
      try {
        if (this.editingExpense) {
          await api.updateExpense(this.editingExpense.izdevums_id, this.expenseForm);
        } else {
          await api.createExpense({
            ...this.expenseForm,
            celojuma_id: this.trip.celojuma_id,
          });
        }
        this.expenseDialog = false;
        await this.loadTrip();
      } catch (err) {
        if (err.status === 422 && err.data?.errors) {
          this.expenseErrors = {};
          for (const k of Object.keys(err.data.errors)) {
            this.expenseErrors[k] = err.data.errors[k][0];
          }
        } else {
          this.expenseServerError = err.message;
        }
      } finally {
        this.expenseSaving = false;
      }
    },

    async deleteExpense(expense) {
      if (!confirm(`Dzēst izdevumu "${expense.kategorija}" € ${expense.summa}?`)) return;
      try {
        await api.deleteExpense(expense.izdevums_id);
        await this.loadTrip();
      } catch (err) {
        alert("Kļūda dzēšot: " + err.message);
      }
    },

    // --- Reservations ---
    openReservationDialog(reservation = null) {
      this.reservationErrors = {};
      this.reservationServerError = "";
      if (reservation) {
        this.editingReservation = reservation;
        this.reservationForm = {
          tips: reservation.tips,
          pakalpojuma_nosaukums: reservation.pakalpojuma_nosaukums,
          cena: Number(reservation.cena),
        };
      } else {
        this.editingReservation = null;
        this.reservationForm = { tips: "", pakalpojuma_nosaukums: "", cena: null };
      }
      this.reservationDialog = true;
    },

    validateReservation() {
      this.reservationErrors = {};
      if (!this.reservationForm.tips)
        this.reservationErrors.tips = "Tips ir obligāts";
      if (!this.reservationForm.pakalpojuma_nosaukums)
        this.reservationErrors.pakalpojuma_nosaukums = "Nosaukums ir obligāts";
      if (this.reservationForm.cena === null || this.reservationForm.cena === "")
        this.reservationErrors.cena = "Cena ir obligāta";
      else if (this.reservationForm.cena < 0)
        this.reservationErrors.cena = "Cena nevar būt negatīva";
      return Object.keys(this.reservationErrors).length === 0;
    },

    async saveReservation() {
      this.reservationServerError = "";
      if (!this.validateReservation()) return;

      this.reservationSaving = true;
      try {
        if (this.editingReservation) {
          await api.updateReservation(this.editingReservation.rezerv_num, this.reservationForm);
        } else {
          await api.createReservation({
            ...this.reservationForm,
            celojuma_id: this.trip.celojuma_id,
          });
        }
        this.reservationDialog = false;
        await this.loadTrip();
      } catch (err) {
        if (err.status === 422 && err.data?.errors) {
          this.reservationErrors = {};
          for (const k of Object.keys(err.data.errors)) {
            this.reservationErrors[k] = err.data.errors[k][0];
          }
        } else {
          this.reservationServerError = err.message;
        }
      } finally {
        this.reservationSaving = false;
      }
    },

    async deleteReservation(reservation) {
      if (!confirm(`Dzēst rezervāciju "${reservation.pakalpojuma_nosaukums}"?`)) return;
      try {
        await api.deleteReservation(reservation.rezerv_num);
        await this.loadTrip();
      } catch (err) {
        alert("Kļūda dzēšot: " + err.message);
      }
    },

    // --- Day points ---
    async openDayPointDialog(point = null) {
      this.dayPointErrors = {};
      this.dayPointServerError = "";
      this.newPlaceErrors = {};
      this.placeMode = "existing";

      this.placesLoading = true;
      try {
        this.places = await api.getPlaces();
      } catch (err) {
        console.error(err);
      } finally {
        this.placesLoading = false;
      }

      if (point) {
        this.editingDayPoint = point;
        this.dayPointForm = {
          vieta_id: point.vieta_id,
          datums: point.datums,
          apraksts: point.apraksts || "",
        };
      } else {
        this.editingDayPoint = null;
        this.dayPointForm = {
          vieta_id: null,
          datums: this.trip.sakuma_datums || "",
          apraksts: "",
        };
      }
      this.newPlaceForm = { nosaukums: "", adrese: "", tips: "", koordinatas: "" };
      this.dayPointDialog = true;
    },

    validateDayPoint() {
      this.dayPointErrors = {};
      this.newPlaceErrors = {};

      if (this.placeMode === "existing") {
        if (!this.dayPointForm.vieta_id)
          this.dayPointErrors.vieta_id = "Vieta ir obligāta";
      } else {
        if (!this.newPlaceForm.nosaukums)
          this.newPlaceErrors.nosaukums = "Nosaukums ir obligāts";
      }

      if (!this.dayPointForm.datums)
        this.dayPointErrors.datums = "Datums ir obligāts";

      return (
        Object.keys(this.dayPointErrors).length === 0 &&
        Object.keys(this.newPlaceErrors).length === 0
      );
    },

    async saveDayPoint() {
      this.dayPointServerError = "";
      if (!this.validateDayPoint()) return;

      this.dayPointSaving = true;
      try {
        let vietaId = this.dayPointForm.vieta_id;

        if (this.placeMode === "new") {
          const newPlace = await api.createPlace(this.newPlaceForm);
          vietaId = newPlace.vieta_id;
        }

        const payload = {
          vieta_id: vietaId,
          datums: this.dayPointForm.datums,
          apraksts: this.dayPointForm.apraksts,
        };

        if (this.editingDayPoint) {
          await api.updateDayPoint(this.editingDayPoint.punkts_id, payload);
        } else {
          await api.createDayPoint({
            ...payload,
            celojuma_id: this.trip.celojuma_id,
          });
        }

        this.dayPointDialog = false;
        await this.loadTrip();
      } catch (err) {
        if (err.status === 422 && err.data?.errors) {
          for (const k of Object.keys(err.data.errors)) {
            if (["nosaukums", "adrese", "tips", "koordinatas"].includes(k)) {
              this.newPlaceErrors[k] = err.data.errors[k][0];
            } else {
              this.dayPointErrors[k] = err.data.errors[k][0];
            }
          }
        } else {
          this.dayPointServerError = err.message;
        }
      } finally {
        this.dayPointSaving = false;
      }
    },

    async deleteDayPoint(point) {
      if (!confirm(`Dzēst punktu "${point.vieta?.nosaukums}"?`)) return;
      try {
        await api.deleteDayPoint(point.punkts_id);
        await this.loadTrip();
      } catch (err) {
        alert("Kļūda dzēšot: " + err.message);
      }
    },
  },
};
</script>

<style scoped>
.detail {
  padding: 40px 20px;
  color: white;
}

.back-btn {
  margin-bottom: 20px;
  border-radius: 999px;
}

.state {
  text-align: center;
  padding: 60px;
  font-size: 18px;
  color: #aaa;
}
.state.error {
  color: #ff6b6b;
}

.hero {
  background: linear-gradient(135deg, #111827, #0f172a);
  padding: 50px;
  border-radius: 20px;
  margin-bottom: 25px;
}

.hero__top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
  margin-bottom: 10px;
}

.hero__top h1 {
  margin-bottom: 0 !important;
}

.hero h1 {
  font-size: 40px;
  font-weight: 900;
  margin-bottom: 15px;
}

.hero__dest,
.hero__dates,
.hero__owner {
  opacity: 0.85;
  margin-bottom: 6px;
}

.hero__budget {
  margin-top: 18px;
  font-size: 22px;
  font-weight: 800;
  color: #f59e0b;
}

.owner-actions {
  display: flex;
  gap: 15px;
  margin-bottom: 25px;
}
.btn {
  border-radius: 999px;
  font-weight: 700;
  padding: 12px 24px;
}
.btn--edit {
  background: rgba(255, 255, 255, 0.08);
  color: white;
}
.btn--delete {
  background: #ef4444;
  color: white;
}
.btn--primary {
  background: linear-gradient(135deg, #f59e0b, #f97316);
  color: #111;
}

.summary {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 15px;
  margin-bottom: 20px;
}

.summary__card {
  background: #0b0f1a;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 18px;
  padding: 25px;
  text-align: center;
}

.summary__num {
  font-size: 28px;
  font-weight: 900;
  color: #f59e0b;
}

.summary__label {
  margin-top: 6px;
  opacity: 0.7;
  font-size: 13px;
}

.budget-bar {
  background: #0b0f1a;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 18px;
  padding: 22px 25px;
  margin-bottom: 20px;
}
.budget-bar__header {
  display: flex;
  justify-content: space-between;
  font-weight: 700;
  margin-bottom: 10px;
  font-size: 15px;
}
.budget-bar__percent.ok { color: #22c55e; }
.budget-bar__percent.warn { color: #f59e0b; }
.budget-bar__percent.over { color: #ef4444; }
.budget-bar__track {
  height: 14px;
  background: rgba(255, 255, 255, 0.08);
  border-radius: 999px;
  overflow: hidden;
}
.budget-bar__fill {
  height: 100%;
  transition: width 0.6s ease;
  border-radius: 999px;
}
.budget-bar__fill.ok {
  background: linear-gradient(90deg, #22c55e, #10b981);
}
.budget-bar__fill.warn {
  background: linear-gradient(90deg, #f59e0b, #f97316);
}
.budget-bar__fill.over {
  background: linear-gradient(90deg, #ef4444, #dc2626);
}
.budget-bar__info {
  font-size: 14px;
  opacity: 0.75;
  margin-top: 8px;
}
.budget-bar__info .over {
  color: #fca5a5;
  margin-left: 4px;
}

.block {
  background: #0b0f1a;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 20px;
  padding: 25px;
  margin-bottom: 20px;
}

.block__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}
.block__header h2 {
  margin-bottom: 0;
}
.add-btn {
  background: linear-gradient(135deg, #f59e0b, #f97316);
  color: #111;
  font-weight: 800;
  border-radius: 999px;
  padding: 8px 18px;
  min-width: auto;
}
.row-btn {
  min-width: auto !important;
  margin-right: 5px;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.08);
  color: white;
}
.row-btn--del {
  background: rgba(239, 68, 68, 0.2);
}

.block h2 {
  font-size: 22px;
  font-weight: 800;
  margin-bottom: 15px;
}

.empty {
  opacity: 0.6;
  font-style: italic;
}

.list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.item {
  display: flex;
  gap: 20px;
  padding: 15px;
  background: rgba(255, 255, 255, 0.04);
  border-radius: 12px;
  position: relative;
}

.item__date {
  min-width: 110px;
  font-weight: 700;
  color: #f59e0b;
}

.item__title {
  font-weight: 700;
}

.item__type {
  opacity: 0.6;
  font-weight: 400;
  font-size: 13px;
}

.item__sub {
  opacity: 0.7;
  font-size: 13px;
}

.item__desc {
  margin-top: 6px;
  opacity: 0.85;
}

.item__actions {
  display: flex;
  gap: 5px;
  align-items: flex-start;
}

.tabs {
  display: flex;
  gap: 10px;
  margin-bottom: 15px;
}
.tab {
  flex: 1;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.08);
  color: white;
}
.tab.active {
  background: linear-gradient(135deg, #f59e0b, #f97316);
  color: #111;
}
.new-place {
  background: rgba(255, 255, 255, 0.04);
  padding: 15px;
  border-radius: 12px;
  margin-bottom: 10px;
}

.table {
  width: 100%;
  border-collapse: collapse;
}

.table th,
.table td {
  text-align: left;
  padding: 10px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.table th {
  opacity: 0.7;
  font-weight: 600;
}

.error {
  background: rgba(239, 68, 68, 0.15);
  color: #fca5a5;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 15px;
}

.dialog {
  background: #0b0f1a;
  color: white;
  padding: 30px;
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.12);
}
.dialog h3 {
  font-size: 22px;
  font-weight: 900;
  margin-bottom: 10px;
}
.dialog p {
  opacity: 0.85;
  margin-bottom: 20px;
}
.dialog__actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

@media (max-width: 900px) {
  .summary {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>