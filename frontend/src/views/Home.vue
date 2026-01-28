<template>
  <v-container fluid class="home">

    <!-- HERO -->
    <section class="hero">
      <div class="hero__overlay"></div>

      <div class="hero__content">
        <h1 class="hero__title">Top frizētava pilsētā</h1>
        <p class="hero__subtitle">
          Moderni griezumi, profesionāli meistari un ātra online rezervācija.
        </p>

        <div class="hero__buttons">
          <v-btn class="btn btn--primary" @click="$router.push('/services')">
            Pakalpojumi
          </v-btn>
          <v-btn class="btn btn--secondary" @click="$router.push('/contact')">
            Kontakti
          </v-btn>
        </div>
      </div>
    </section>

    <!-- WHY US -->
    <section class="section section--dark">
      <h2 class="section__title">Kāpēc izvēlēties mūs</h2>

      <div class="features">
        <div class="feature">
          ✂️
          <h3>Profesionāli meistari</h3>
          <p>Ilggadēja pieredze un modernākie stili.</p>
        </div>

        <div class="feature">
          ⚡
          <h3>Ātra rezervācija</h3>
          <p>Rezervē savu laiku dažu sekunžu laikā.</p>
        </div>

        <div class="feature">
          💎
          <h3>Kvalitāte</h3>
          <p>Tikai pārbaudīti instrumenti un produkti.</p>
        </div>
      </div>
    </section>

    <!-- SERVICES -->
    <section class="section section--soft">
      <h2 class="section__title">Mūsu pakalpojumi</h2>

      <div class="cards">
        <div class="card" v-for="s in services" :key="s.id">
          <div class="card__icon">✂️</div>
          <div class="card__title">{{ s.name }}</div>
          <div class="card__desc">{{ s.description }}</div>
          <div class="card__price">€ {{ s.price }}</div>

          <v-btn
            class="btn btn--primary btn--full"
            @click="reserve(s.name)"
          >
            Rezervēt
          </v-btn>
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

    <!-- CTA -->
    <section class="section cta">
      <h2>Gatavs jaunam izskatam?</h2>
      <v-btn class="btn btn--primary btn--big" @click="$router.push('/services')">
        Rezervēt tagad
      </v-btn>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
      © 2026 Frizētava. Visas tiesības aizsargātas.
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
      .then(res => res.json())
      .then(data => {
        this.services = data;
      });
  },

  methods: {
    reserve(serviceName) {
      alert("Rezervācija: " + serviceName);
    },
  },
};
</script>

<style scoped>
.home {
  font-family: Arial, sans-serif;
}

/* HERO */
.hero {
  position: relative;
  min-height: 85vh;
  background: url("https://cdn.prod.website-files.com/644a9d9ce529ef8812f82a28/647fb85c69e95444243ef9bd_Henley%27s%20Gentlemen%27s%20Grooming%20-%20Barbershop%20and%20Mens%20Grooming.webp")
    center / cover no-repeat;
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
}

/* BUTTONS */
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
  border: 2px solid rgba(255,255,255,0.6);
  color: white;
}

.btn--secondary:hover {
  background: rgba(255,255,255,0.1);
}

.btn--full {
  width: 100%;
  margin-top: 15px;
}

.btn--big {
  font-size: 18px;
  padding: 14px 36px;
}

/* SECTIONS */
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

/* FEATURES */
.features {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
  text-align: center;
}

.feature {
  padding: 35px;
  background: rgba(255,255,255,0.06);
  border-radius: 22px;
}

/* CARDS */
.cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 25px;
}

.card {
  padding: 50px;
  background: rgba(255,255,255,0.06);
  border-radius: 24px;
  transition: transform 0.25s, box-shadow 0.25s;
}

.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 15px 40px rgba(0,0,0,0.35);
}

.card__title {
  font-size: 20px;
  font-weight: 700;
  margin: 10px 0;
}

.card__price {
  font-size: 18px;
  font-weight: 700;
  margin-top: 10px;
  margin-bottom: 18px;
}

/* STEPS */
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

/* CTA */
.cta {
  text-align: center;
  background: linear-gradient(135deg, #d1c3d1, #7a716b);
  color: #111;
}

/* FOOTER */
.footer {
  padding: 30px;
  text-align: center;
  background: #0b0f1a;
  color: #aaa;
}

/* RESPONSIVE */
@media (max-width: 900px) {
  .cards,
  .features,
  .steps {
    grid-template-columns: 1fr;
  }

  .hero {
    padding: 60px 30px;
  }

  .hero__title {
    font-size: 36px;
  }
}
</style>
