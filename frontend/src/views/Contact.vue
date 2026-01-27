<template>
  <v-container class="contact" fluid>
    <section class="contact__hero">
      <h1>Kontakti</h1>
      <p>Raksti vai zvanī — mēs atbildēsim 24h laikā.</p>
    </section>

    <section class="contact__content">
      <div class="contact__info">
        <div class="info__item"><b>Adrese:</b> Brīvības iela 10, Rīga</div>
        <div class="info__item"><b>Telefons:</b> +371 12345678</div>
        <div class="info__item"><b>E-pasts:</b> info@frizetava.lv</div>
      </div>

      <div class="contact__form">
        <v-card class="form-card">
          <v-card-text>
            <v-text-field v-model="name" label="Vārds" />
            <v-text-field v-model="email" label="E-pasts" />
            <v-textarea v-model="message" label="Ziņa" rows="4" />
          </v-card-text>
          <v-card-actions>
            <v-btn color="primary" @click="send">Sūtīt</v-btn>
          </v-card-actions>
        </v-card>
      </div>
    </section>
  </v-container>
</template>

<script>
export default {
  data() {
    return {
      name: "",
      email: "",
      message: "",
    };
  },
  methods: {
    send() {
      alert("Ziņa nosūtīta! Paldies, " + this.name);
      this.name = "";
      this.email = "";
      this.message = "";
    },
  },
};
</script>

<style scoped>
.contact {
  padding: 40px 20px;
}

.contact__hero {
  background: linear-gradient(135deg, #111827, #0f172a);
  color: white;
  padding: 60px 20px;
  border-radius: 20px;
  text-align: center;
}

.contact__hero h1 {
  font-size: 44px;
  font-weight: 900;
}

.contact__hero p {
  opacity: 0.8;
  margin-top: 10px;
}

.contact__content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-top: 30px;
}

.contact__info {
  background: #0b0f1a;
  padding: 25px;
  border-radius: 20px;
  color: white;
  border: 1px solid rgba(255,255,255,0.12);
}

.info__item {
  margin-bottom: 12px;
  opacity: 0.85;
}

.form-card {
  border-radius: 20px;
}

/* RESPONSIVE */
@media (max-width: 900px) {
  .contact__content {
    grid-template-columns: 1fr;
  }
}
</style>
