<template>
  <v-container class="admin" fluid>
    <section class="hero">
      <h1>🛡 Administratora panelis</h1>
      <p>Sistēmas pārvaldība un satura moderācija</p>
    </section>

    <div class="tabs">
      <button
        v-for="t in tabs"
        :key="t.id"
        :class="['tab', { active: activeTab === t.id }]"
        @click="activeTab = t.id"
      >
        {{ t.label }}
      </button>
    </div>

    <section v-if="activeTab === 'stats'" class="block">
      <Loader v-if="loadingStats" />
      <div v-else-if="stats" class="stat-grid">
        <div class="stat-card">
          <div class="stat-card__num">{{ stats.users_total }}</div>
          <div class="stat-card__label">Lietotāji kopā</div>
        </div>
        <div class="stat-card">
          <div class="stat-card__num">{{ stats.users_registered }}</div>
          <div class="stat-card__label">Reģistrēti lietotāji</div>
        </div>
        <div class="stat-card">
          <div class="stat-card__num">{{ stats.users_admins }}</div>
          <div class="stat-card__label">Administratori</div>
        </div>
        <div class="stat-card">
          <div class="stat-card__num">{{ stats.trips_total }}</div>
          <div class="stat-card__label">Ceļojumi</div>
        </div>
        <div class="stat-card">
          <div class="stat-card__num">{{ stats.places_total }}</div>
          <div class="stat-card__label">Vietas</div>
        </div>
        <div class="stat-card">
          <div class="stat-card__num">{{ stats.messages_total }}</div>
          <div class="stat-card__label">Ziņas</div>
        </div>
      </div>
    </section>

    <section v-if="activeTab === 'users'" class="block">
      <Loader v-if="loadingUsers" />
      <div v-else-if="users.length === 0" class="empty">Nav lietotāju.</div>
      <div v-else class="table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Vārds, Uzvārds</th>
              <th>E-pasts</th>
              <th>Loma</th>
              <th>Ceļojumi</th>
              <th style="width: 120px;">Darbības</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="u in users" :key="u.id">
              <td>{{ u.id }}</td>
              <td>{{ u.name }} {{ u.uzvards }}</td>
              <td>{{ u.email }}</td>
              <td>
                <span :class="['badge', 'badge--' + u.loma.toLowerCase()]">
                  {{ u.loma }}
                </span>
              </td>
              <td>{{ u.celojumi_count }}</td>
              <td>
                <v-btn
                  v-if="u.loma !== 'Admins'"
                  size="small"
                  class="btn--del"
                  @click="deleteUser(u)"
                >
                  🗑 Dzēst
                </v-btn>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <section v-if="activeTab === 'trips'" class="block">
      <Loader v-if="loadingTrips" />
      <div v-else-if="trips.length === 0" class="empty">Nav ceļojumu.</div>
      <div v-else class="table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nosaukums</th>
              <th>Galamērķis</th>
              <th>Valsts</th>
              <th>Īpašnieks</th>
              <th>Datumi</th>
              <th>Budžets</th>
              <th style="width: 160px;">Darbības</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="t in trips" :key="t.celojuma_id">
              <td>{{ t.celojuma_id }}</td>
              <td>{{ t.nosaukums }}</td>
              <td>{{ t.galamerkis }}</td>
              <td>{{ t.valsts || "—" }}</td>
              <td v-if="t.lietotajs">{{ t.lietotajs.name }} {{ t.lietotajs.uzvards }}</td>
              <td v-else>—</td>
              <td>{{ formatDate(t.sakuma_datums) }} – {{ formatDate(t.beigu_datums) }}</td>
              <td>€ {{ t.budzets }}</td>
              <td>
                <v-btn size="small" class="btn--view" @click="$router.push('/services/' + t.celojuma_id)">
                  👁
                </v-btn>
                <v-btn size="small" class="btn--del" @click="deleteTrip(t)">
                  🗑
                </v-btn>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <section v-if="activeTab === 'messages'" class="block">
      <Loader v-if="loadingMessages" />
      <div v-else-if="messages.length === 0" class="empty">Nav ziņu.</div>
      <div v-else class="messages-list">
        <div v-for="m in messages" :key="m.message_id" class="msg">
          <div class="msg__header">
            <div>
              <strong>{{ m.vards }}</strong>
              &lt;{{ m.epasts }}&gt;
            </div>
            <div class="msg__date">{{ formatDateTime(m.created_at) }}</div>
          </div>
          <div class="msg__body">{{ m.zina }}</div>
          <div class="msg__actions">
            <v-btn size="small" class="btn--del" @click="deleteMessage(m)">
              🗑 Dzēst
            </v-btn>
          </div>
        </div>
      </div>
    </section>
  </v-container>
</template>

<script>
import api from "@/api";
import Loader from "@/components/Loader.vue";
import { useAuthStore } from "@/stores/auth";

export default {
  components: { Loader },
  data() {
    return {
      activeTab: "stats",
      tabs: [
        { id: "stats", label: "📊 Statistika" },
        { id: "users", label: "👥 Lietotāji" },
        { id: "trips", label: "🌍 Ceļojumi" },
        { id: "messages", label: "✉️ Ziņas" },
      ],

      stats: null,
      users: [],
      trips: [],
      messages: [],

      loadingStats: false,
      loadingUsers: false,
      loadingTrips: false,
      loadingMessages: false,
    };
  },

  watch: {
    activeTab(newTab) {
      this.loadTab(newTab);
    },
  },

  async mounted() {
    const auth = useAuthStore();
    if (!auth.isLoggedIn) {
      this.$router.push("/login");
      return;
    }
    if (!auth.isAdmin) {
      alert("Nav administratora tiesību");
      this.$router.push("/");
      return;
    }
    await this.loadTab(this.activeTab);
  },

  methods: {
    async loadTab(tab) {
      try {
        if (tab === "stats") {
          this.loadingStats = true;
          this.stats = await api.adminStats();
          this.loadingStats = false;
        } else if (tab === "users") {
          this.loadingUsers = true;
          this.users = await api.adminUsers();
          this.loadingUsers = false;
        } else if (tab === "trips") {
          this.loadingTrips = true;
          this.trips = await api.adminTrips();
          this.loadingTrips = false;
        } else if (tab === "messages") {
          this.loadingMessages = true;
          this.messages = await api.adminMessages();
          this.loadingMessages = false;
        }
      } catch (err) {
        alert("Kļūda: " + err.message);
        this.loadingStats = false;
        this.loadingUsers = false;
        this.loadingTrips = false;
        this.loadingMessages = false;
      }
    },

    async deleteUser(user) {
      if (!confirm(`Dzēst lietotāju "${user.name} ${user.uzvards}"? Tiks dzēsti arī viņa ceļojumi.`)) return;
      try {
        await api.adminDeleteUser(user.id);
        this.users = this.users.filter((u) => u.id !== user.id);
      } catch (err) {
        alert("Kļūda dzēšot: " + err.message);
      }
    },

    async deleteTrip(trip) {
      if (!confirm(`Dzēst ceļojumu "${trip.nosaukums}"?`)) return;
      try {
        await api.adminDeleteTrip(trip.celojuma_id);
        this.trips = this.trips.filter((t) => t.celojuma_id !== trip.celojuma_id);
      } catch (err) {
        alert("Kļūda dzēšot: " + err.message);
      }
    },

    async deleteMessage(msg) {
      if (!confirm(`Dzēst ziņu no "${msg.vards}"?`)) return;
      try {
        await api.adminDeleteMessage(msg.message_id);
        this.messages = this.messages.filter((m) => m.message_id !== msg.message_id);
      } catch (err) {
        alert("Kļūda dzēšot: " + err.message);
      }
    },

    formatDate(d) {
      if (!d) return "";
      return new Date(d).toLocaleDateString("lv-LV");
    },
    formatDateTime(d) {
      if (!d) return "";
      const date = new Date(d);
      return date.toLocaleDateString("lv-LV") + " " + date.toLocaleTimeString("lv-LV", { hour: "2-digit", minute: "2-digit" });
    },
  },
};
</script>

<style scoped>
.admin {
  padding: 40px 20px;
  color: white;
}

.hero {
  background: linear-gradient(135deg, #4c1d95, #1e293b);
  padding: 50px;
  border-radius: 20px;
  text-align: center;
  margin-bottom: 25px;
  border: 1px solid rgba(167, 139, 250, 0.3);
}
.hero h1 {
  font-size: 38px;
  font-weight: 900;
}
.hero p {
  opacity: 0.85;
  margin-top: 8px;
}

.tabs {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}
.tab {
  background: rgba(255, 255, 255, 0.06);
  color: rgba(255, 255, 255, 0.85);
  border: 1px solid rgba(255, 255, 255, 0.1);
  padding: 12px 22px;
  border-radius: 999px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}
.tab:hover {
  background: rgba(255, 255, 255, 0.12);
}
.tab.active {
  background: linear-gradient(135deg, #8b5cf6, #6366f1);
  color: white;
  border-color: transparent;
  box-shadow: 0 6px 20px rgba(139, 92, 246, 0.35);
}

.block {
  background: #0b0f1a;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 20px;
  padding: 25px;
}

.empty {
  opacity: 0.6;
  font-style: italic;
  text-align: center;
  padding: 40px;
}

.stat-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
}
.stat-card {
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 25px;
  text-align: center;
}
.stat-card__num {
  font-size: 32px;
  font-weight: 900;
  color: #a78bfa;
}
.stat-card__label {
  margin-top: 8px;
  opacity: 0.7;
  font-size: 13px;
}

.table {
  width: 100%;
  border-collapse: collapse;
}
.table th,
.table td {
  text-align: left;
  padding: 12px 8px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.table th {
  opacity: 0.7;
  font-weight: 600;
  font-size: 13px;
  text-transform: uppercase;
}

.badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
}
.badge--admins {
  background: rgba(139, 92, 246, 0.2);
  color: #c4b5fd;
}
.badge--registrets {
  background: rgba(34, 197, 94, 0.2);
  color: #86efac;
}
.badge--viesis {
  background: rgba(255, 255, 255, 0.1);
  color: #d1d5db;
}

.btn--del {
  background: rgba(239, 68, 68, 0.2);
  color: #fca5a5;
  border-radius: 8px;
  margin-right: 5px;
}
.btn--view {
  background: rgba(255, 255, 255, 0.08);
  color: white;
  border-radius: 8px;
  margin-right: 5px;
}

.messages-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.msg {
  background: rgba(255, 255, 255, 0.04);
  border-radius: 14px;
  padding: 18px;
  border-left: 3px solid #a78bfa;
}
.msg__header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
  font-size: 14px;
}
.msg__date {
  opacity: 0.6;
  font-size: 12px;
}
.msg__body {
  white-space: pre-wrap;
  margin-bottom: 12px;
  line-height: 1.5;
}
.msg__actions {
  display: flex;
  justify-content: flex-end;
}

.table-wrap {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  margin: 0 -10px;
  padding: 0 10px;
}
.table-wrap .table {
  min-width: 600px;
}

@media (max-width: 1100px) {
  .stat-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 700px) {
  .admin {
    padding: 25px 15px;
  }
  .hero {
    padding: 30px 20px;
  }
  .hero h1 {
    font-size: 26px;
  }
  .stat-grid {
    grid-template-columns: 1fr;
  }
  .table {
    font-size: 12px;
  }
  .table th, .table td {
    padding: 8px 4px;
  }
  .msg__header {
    flex-direction: column;
    gap: 4px;
  }
}
</style>