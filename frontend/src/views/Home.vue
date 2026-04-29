<template>
  <v-container fluid class="home">

    <section class="hero">
      <div class="hero__overlay"></div>

      <div class="hero__content">
        <h1 class="hero__title">Plāno savu sapņu ceļojumu</h1>
        <p class="hero__subtitle">
          Izveido un pārvaldi savus ceļojumus vienuviet – galamērķi, budžets un aktivitātes.
        </p>

        <div class="hero__buttons">
          <v-btn class="btn btn--primary" @click="$router.push('/services')">
            Skatīt ceļojumus
          </v-btn>
          <v-btn class="btn btn--secondary" @click="$router.push('/contact')">
            Sazināties
          </v-btn>
        </div>
      </div>
    </section>

    <section class="section section--dark">
      <h2 class="section__title">Kāpēc izvēlēties mūsu ceļojumu plānotāju</h2>

      <div class="features">
        <div class="feature">
          🌍
          <h3>Dažādi galamērķi</h3>
          <p>Izvēlies no populārākajiem un eksotiskākajiem galamērķiem.</p>
        </div>

        <div class="feature">
          💰
          <h3>Budžeta kontrole</h3>
          <p>Plāno savus izdevumus un kontrolē kopējās izmaksas.</p>
        </div>

        <div class="feature">
          🗂
          <h3>Ērta pārvaldība</h3>
          <p>Visi tavi ceļojumi vienuviet – vienkārši un pārskatāmi.</p>
        </div>
      </div>
    </section>

    <section class="section section--soft">
      <h2 class="section__title">Tūvakie ceļojumi</h2>

      <Loader v-if="loading" />
      <div v-else-if="error" class="error">⚠️ Neizdevās ielādēt ceļojumus. Lūdzu, mēģini vēlreiz.</div>
      <EmptyState
        v-else-if="trips.length === 0"
        icon="🗓️"
        title="Pagaidām nav plānotu ceļojumu"
        subtitle="Esi pirmais, kas izveido jaunu ceļojuma plānu!"
      />

      <div v-else class="cards">
        <div class="card" v-for="t in trips" :key="t.celojuma_id">
          <div class="card__icon">🌍</div>
          <div class="card__title">{{ t.nosaukums }}</div>
          <div class="card__desc">{{ t.galamerkis }}</div>
          <div class="card__dates">
            {{ formatDate(t.sakuma_datums) }} – {{ formatDate(t.beigu_datums) }}
          </div>
          <div class="card__price">€ {{ t.budzets }}</div>

          <v-btn
            class="btn btn--primary btn--full"
            @click="$router.push('/services/' + t.celojuma_id)"
          >
            Skatīt detaļas
          </v-btn>
        </div>
      </div>
    </section>

    <section class="section section--light">
      <h2 class="section__title">Kā tas darbojas</h2>

      <div class="steps">
        <div class="step">
          <div class="step__num">1</div>
          <div class="step__text">Izvēlies ceļojumu</div>
        </div>

        <div class="step">
          <div class="step__num">2</div>
          <div class="step__text">Pārskati informāciju un budžetu</div>
        </div>

        <div class="step">
          <div class="step__num">3</div>
          <div class="step__text">Saglabā un plāno savu piedzīvojumu</div>
        </div>
      </div>
    </section>

    <section class="section cta">
      <h2>Gatavs jaunam piedzīvojumam?</h2>
      <v-btn class="btn btn--primary btn--big" @click="$router.push('/services')">
        Sākt plānot
      </v-btn>
    </section>

  </v-container>
</template>

<script>
import api from "@/api";
import Loader from "@/components/Loader.vue";
import EmptyState from "@/components/EmptyState.vue";

export default {
  components: { Loader, EmptyState },
  data() {
    return {
      trips: [],
      loading: true,
      error: null,
    };
  },
  async mounted() {
    try {
      const today = new Date().toISOString().slice(0, 10);
      const data = await api.getTrips({
        date_from: today,
        sort_by: "sakuma_datums",
        sort_dir: "asc",
      });
      this.trips = data.slice(0, 3);
    } catch (err) {
      this.error = err.message;
    } finally {
      this.loading = false;
    }
  },
  methods: {
    formatDate(d) {
      if (!d) return "";
      return new Date(d).toLocaleDateString("lv-LV");
    },
  },
};
</script>

<style scoped>
.home {
  font-family: Arial, sans-serif;
}

.hero {
  position: relative;
  min-height: 85vh;
  background: url("https://wallpapers.com/images/hd/plane-desktop-c5zffr0rhiqxhibo.jpg")
    center / cover no-repeat;
  background-position-y: 10%;
  display: flex;
  align-items: center;
  padding: 80px 60px;
  color: white;
}

.hero__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    135deg,
    rgba(10, 15, 30, 0.95),
    rgba(10, 15, 30, 0.6)
  );
}

.hero__content {
  position: relative;
  max-width: 620px;
}

.hero__title {
  font-size: 54px;
  font-weight: 900;
  margin-bottom: 20px;
}

.hero__subtitle {
  font-size: 18px;
  margin-bottom: 35px;
  opacity: 0.85;
}

.hero__buttons {
  display: flex;
  gap: 15px;
  cursor: pointer;
}

.btn {
  border-radius: 100px;
  font-weight: 700;
  padding: 8px 21px;
  transition: all 0.25s ease;
}

.btn--primary {
  background: linear-gradient(135deg, #f59e0b, #f97316);
  color: #111;
  box-shadow: 0 8px 25px rgba(245, 158, 11, 0.35);
}

.btn--primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(245, 158, 11, 0.5);
}

.btn--secondary {
  border: 2px solid rgba(255, 255, 255, 0.6);
  color: white;
}

.btn--secondary:hover {
  background: rgba(255, 255, 255, 0.1);
}

.btn--full {
  width: 100%;
  margin-top: 15px;
}

.btn--big {
  font-size: 18px;
  padding: 14px 36px;
}

.section {
  padding: 80px 60px;
}

.section--dark {
  background: #0b0f1a;
  color: white;
}

.section--soft {
  background: #111827;
  color: white;
}

.section--light {
  background: #f5f5f5;
  color: #111;
}

.section__title {
  text-align: center;
  font-size: 34px;
  font-weight: 800;
  margin-bottom: 45px;
}

.loading, .error {
  text-align: center;
  color: white;
  font-size: 20px;
  padding: 40px;
}
.error { color: #ff6b6b; }

.features {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
  text-align: center;
}

.feature {
  padding: 35px;
  background: rgba(255, 255, 255, 0.06);
  border-radius: 22px;
}

.cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 25px;
}

.card {
  padding: 50px;
  background: rgba(255, 255, 255, 0.06);
  border-radius: 24px;
  transition: transform 0.35s ease, box-shadow 0.35s ease, background 0.35s ease;
  cursor: pointer;
  text-align: center;
}

.card:hover {
  transform: translateY(-12px) scale(1.03);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
  background: rgba(255, 255, 255, 0.12);
}

.card__title {
  font-size: 20px;
  font-weight: 700;
  margin: 10px 0;
}

.card__desc {
  opacity: 0.8;
  margin-bottom: 10px;
}

.card__dates {
  font-size: 14px;
  opacity: 0.7;
  margin-bottom: 10px;
}

.card__price {
  font-size: 18px;
  font-weight: 700;
  margin-top: 10px;
  margin-bottom: 18px;
  color: #f59e0b;
}

.steps {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 25px;
}

.step {
  background: white;
  padding: 30px;
  border-radius: 24px;
  text-align: center;
}

.step__num {
  font-size: 32px;
  font-weight: 900;
  color: #f59e0b;
}

.cta {
  text-align: center;
  background: linear-gradient(135deg, #d1c3d1, #7a716b);
  color: #111;
}

.footer {
  padding: 30px;
  text-align: center;
  background: #0b0f1a;
  color: #aaa;
}

@media (max-width: 1100px) {
  .features,
  .cards,
  .steps {
    grid-template-columns: 1fr 1fr;
  }
  .section {
    padding: 60px 30px;
  }
}

@media (max-width: 700px) {
  .features,
  .cards,
  .steps {
    grid-template-columns: 1fr;
  }
  .hero {
    padding: 50px 20px;
    min-height: 70vh;
  }
  .hero__title {
    font-size: 32px;
  }
  .hero__subtitle {
    font-size: 16px;
  }
  .hero__buttons {
    flex-direction: column;
    width: 100%;
  }
  .hero__buttons .btn {
    width: 100%;
  }
  .section {
    padding: 50px 20px;
  }
  .section__title {
    font-size: 26px;
    margin-bottom: 30px;
  }
  .feature {
    padding: 25px;
  }
  .card {
    padding: 30px;
  }
}
</style>