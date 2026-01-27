<template>
  <v-container fluid class="home">
    <!-- HERO -->
    <section class="hero">
      <div class="hero__content">
        <h1 class="hero__title">Top frizētava pilsētā</h1>
        <p class="hero__subtitle">
          Moderni griezumi, ātri un kvalitatīvi. Rezervē savu laiku online.
        </p>
        <div class="hero__buttons">
          <v-btn class="btn btn--primary" @click="goToServices">
            Pakalpojumi
          </v-btn>
          <v-btn class="btn btn--secondary" @click="goToContact">
            Kontakti
          </v-btn>
        </div>
      </div>
      <div class="hero__image">
        <img src="https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=1200&q=80" />
      </div>
    </section>

    <!-- SERVICES -->
    <section class="section">
      <h2 class="section__title">Mūsu pakalpojumi</h2>
      <div class="cards">
        <div class="card" v-for="s in services" :key="s.id">
          <div class="card__icon">✂️</div>
          <div class="card__title">{{ s.name }}</div>
          <div class="card__desc">{{ s.description }}</div>
          <div class="card__price">€ {{ s.price }}</div>
        </div>
      </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="section section--light">
      <h2 class="section__title">Kā tas notiek</h2>
      <div class="steps">
        <div class="step">
          <div class="step__num">1</div>
          <div class="step__text">Izvēlies pakalpojumu</div>
        </div>
        <div class="step">
          <div class="step__num">2</div>
          <div class="step__text">Rezervē laiku</div>
        </div>
        <div class="step">
          <div class="step__num">3</div>
          <div class="step__text">Nāc un izbaudi</div>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
      <div class="footer__text">© 2026 Frizētava. Visas tiesības aizsargātas.</div>
    </footer>
  </v-container>
</template>

<script>
export default {
  data() {
    return {
      services: [],
    };
  },
  mounted() {
    fetch("http://127.0.0.1:8000/api/services")
      .then((response) => response.json())
      .then((data) => {
        this.services = data;
      })
      .catch((err) => {
        console.log(err);
      });
  },
  methods: {
    goToServices() {
      this.$router.push("/services");
    },
    goToContact() {
      this.$router.push("/contact");
    },
  },
};
</script>

<style scoped>
.home {
  font-family: 'Arial', sans-serif;
}

/* HERO */
.hero {
  display: grid;
  grid-template-columns: 1.1fr 0.9fr;
  gap: 40px;
  padding: 80px 60px;
  background: linear-gradient(135deg, #111827, #0f172a);
  color: white;
}

.hero__title {
  font-size: 48px;
  margin-bottom: 20px;
  font-weight: 800;
}

.hero__subtitle {
  font-size: 18px;
  margin-bottom: 30px;
  opacity: 0.8;
}

.hero__buttons .btn {
  margin-right: 15px;
}

.btn {
  padding: 12px 20px;
  border-radius: 999px;
  font-weight: 700;
}

.btn--primary {
  background: #f59e0b;
  color: #111;
}

.btn--secondary {
  border: 1px solid rgba(255,255,255,0.4);
  color: white;
  background: transparent;
}

.hero__image img {
  width: 100%;
  border-radius: 20px;
  object-fit: cover;
}

/* SECTIONS */
.section {
  padding: 60px 60px;
  background: #0b0f1a;
  color: white;
}

.section--light {
  background: #f5f5f5;
  color: #111;
}

.section__title {
  text-align: center;
  font-size: 32px;
  margin-bottom: 40px;
  font-weight: 800;
}

/* CARDS */
.cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.card {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.15);
  border-radius: 20px;
  padding: 25px;
  transition: transform 0.2s ease, background 0.2s ease;
}

.card:hover {
  transform: translateY(-8px);
  background: rgba(255,255,255,0.08);
}

.card__icon {
  font-size: 26px;
  margin-bottom: 15px;
}

.card__title {
  font-weight: 800;
  margin-bottom: 10px;
}

.card__desc {
  opacity: 0.8;
  margin-bottom: 15px;
}

.card__price {
  font-weight: 900;
  font-size: 18px;
}

/* STEPS */
.steps {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.step {
  padding: 25px;
  background: white;
  border-radius: 20px;
  text-align: center;
  box-shadow: 0 10px 20px rgba(0,0,0,0.08);
}

.step__num {
  width: 50px;
  height: 50px;
  border-radius: 999px;
  background: #f59e0b;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 900;
  margin: 0 auto 15px;
}

.step__text {
  font-weight: 800;
}

/* FOOTER */
.footer {
  padding: 30px 60px;
  background: #0b0f1a;
  color: white;
  text-align: center;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 900px) {
  .hero {
    grid-template-columns: 1fr;
    padding: 60px 30px;
  }

  .hero__title {
    font-size: 38px;
  }

  .section {
    padding: 50px 30px;
  }

  .section__title {
    font-size: 28px;
  }

  .cards {
    grid-template-columns: repeat(2, 1fr);
  }

  .steps {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 600px) {
  .hero {
    padding: 40px 20px;
  }

  .hero__title {
    font-size: 30px;
  }

  .hero__subtitle {
    font-size: 16px;
  }

  .cards {
    grid-template-columns: 1fr;
  }

  .steps {
    grid-template-columns: 1fr;
  }

  .section {
    padding: 40px 20px;
  }

  .footer {
    padding: 20px 20px;
  }
}
</style>
