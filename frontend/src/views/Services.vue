<template>
  <v-container class="services" fluid>
    <section class="hero">
      <div class="hero__text">
        <h1>Ceļojumi</h1>
        <p>Izvēlies ceļojumu un plāno savu nākamo piedzīvojumu ērti un vienkārši.</p>
      </div>
    </section>

    <section class="cards">
      <div
        class="card"
        v-for="s in services"
        :key="s.id"
      >
        <div class="card__icon">🌍</div>
        <div class="card__name">{{ s.name }}</div>
        <div class="card__desc">{{ s.description }}</div>
        <div class="card__price">€ {{ s.price }}</div>
        <v-btn class="card__btn" @click="book(s.name)">Skatīt detaļas</v-btn>
      </div>
    </section>
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
      .then((res) => res.json())
      .then((data) => (this.services = data));
  },
  methods: {
    book(name) {
      alert("Rezervācija: " + name);
    },
  },
};
</script>

<style scoped>
.services {
  padding: 40px 20px;
}

.hero {
  background: linear-gradient(135deg, #111827, #0f172a);
  padding: 60px 20px;
  border-radius: 20px;
  color: white;
  text-align: center;
}

.hero h1 {
  font-size: 44px;
  font-weight: 900;
}

.hero p {
  opacity: 0.8;
  margin-top: 10px;
}

.cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-top: 30px;
}

.card {
  background: #0b0f1a;
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 20px;
  padding: 25px;
  text-align: center;
  transition: transform 0.2s ease, background 0.2s ease;
}

.card:hover {
  transform: translateY(-10px);
  background: rgba(255,255,255,0.08);
}

.card__icon {
  font-size: 30px;
  margin-bottom: 15px;
}

.card__name {
  font-weight: 900;
  font-size: 20px;
}

.card__desc {
  opacity: 0.8;
  margin: 10px 0 15px;
}

.card__price {
  font-weight: 900;
  color: #f59e0b;
  margin-bottom: 15px;
}

.card__btn {
  border-radius: 999px;
  padding: 10px 20px;
}

@media (max-width: 900px) {
  .cards {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 600px) {
  .cards {
    grid-template-columns: 1fr;
  }
}
</style>
