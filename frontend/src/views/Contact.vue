<template>
  <v-container class="contact" fluid>
    <section class="contact__hero">
      <h1>Sazināties ar mums</h1>
      <p>Ja tev ir jautājumi par ceļojumu plānošanu, raksti mums — atbildēsim 24h laikā.</p>
    </section>

    <section class="contact__content">
      <div class="contact__info">
        <h3>Kontaktinformācija</h3>
        <div class="info__item"><b>📍 Adrese:</b> Brīvības iela 10, Rīga</div>
        <div class="info__item"><b>📞 Telefons:</b> +371 12345678</div>
        <div class="info__item"><b>✉️ E-pasts:</b> info@celojumi.lv</div>
        <div class="info__item"><b>🕒 Darba laiks:</b> P-P 9:00 – 18:00</div>

        <div class="info__note">
          Mēs parasti atbildam 24 stundu laikā darba dienās.
        </div>
      </div>

      <div class="contact__form">
        <v-card class="form-card">
          <v-card-text>
            <h3>Sūtīt ziņu</h3>

            <v-text-field
              v-model="form.vards"
              label="Vārds"
              variant="outlined"
              :error-messages="errors.vards"
              maxlength="50"
              :disabled="sending"
            />
            <v-text-field
              v-model="form.epasts"
              label="E-pasts"
              type="email"
              variant="outlined"
              :error-messages="errors.epasts"
              maxlength="100"
              :disabled="sending"
            />
            <v-textarea
              v-model="form.zina"
              label="Ziņa"
              rows="5"
              variant="outlined"
              :error-messages="errors.zina"
              maxlength="2000"
              counter
              :disabled="sending"
            />

            <div v-if="serverError" class="error">{{ serverError }}</div>
            <div v-if="success" class="success">
              ✅ Paldies, {{ lastSentName }}! Tava ziņa ir nosūtīta.
            </div>
          </v-card-text>

          <v-card-actions>
            <v-btn
              class="submit"
              :loading="sending"
              @click="send"
            >
              Nosūtīt ziņu
            </v-btn>
          </v-card-actions>
        </v-card>
      </div>
    </section>
  </v-container>
</template>

<script>
import api from "@/api";

export default {
  data() {
    return {
      form: { vards: "", epasts: "", zina: "" },
      errors: {},
      serverError: "",
      success: false,
      sending: false,
      lastSentName: "",
    };
  },
  methods: {
    validate() {
      this.errors = {};
      if (!this.form.vards) this.errors.vards = "Vārds ir obligāts";
      if (!this.form.epasts) this.errors.epasts = "E-pasts ir obligāts";
      else if (!/^\S+@\S+\.\S+$/.test(this.form.epasts))
        this.errors.epasts = "E-pasta formāts nav pareizs";
      if (!this.form.zina) this.errors.zina = "Ziņa ir obligāta";
      else if (this.form.zina.length < 5)
        this.errors.zina = "Ziņai jābūt vismaz 5 simboli";
      return Object.keys(this.errors).length === 0;
    },
    async send() {
      this.serverError = "";
      this.success = false;
      if (!this.validate()) return;

      this.sending = true;
      try {
        await api.sendMessage(this.form);
        this.lastSentName = this.form.vards;
        this.form = { vards: "", epasts: "", zina: "" };
        this.success = true;
      } catch (err) {
        if (err.status === 422 && err.data?.errors) {
          this.errors = {};
          for (const k of Object.keys(err.data.errors)) {
            this.errors[k] = err.data.errors[k][0];
          }
        } else {
          this.serverError = "Diemžēl neizdevās nosūtīt ziņu. Lūdzu, mēģini vēlreiz.";
        }
      } finally {
        this.sending = false;
      }
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
  padding: 35px;
  border-radius: 20px;
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.12);
}

.contact__info h3 {
  font-size: 22px;
  font-weight: 800;
  margin-bottom: 18px;
}

.info__item {
  margin-bottom: 14px;
  opacity: 0.9;
  font-size: 15px;
}

.info__note {
  margin-top: 25px;
  padding: 14px;
  background: rgba(245, 158, 11, 0.08);
  border-left: 3px solid #f59e0b;
  border-radius: 8px;
  font-size: 14px;
  opacity: 0.9;
}

.form-card {
  border-radius: 20px;
  background: #0b0f1a !important;
  color: white;
  padding: 10px;
}
.form-card h3 {
  font-size: 22px;
  font-weight: 800;
  margin-bottom: 15px;
  color: white;
}

.error {
  background: rgba(239, 68, 68, 0.15);
  color: #fca5a5;
  padding: 12px;
  border-radius: 8px;
  margin-top: 10px;
  font-size: 14px;
}

.success {
  background: rgba(34, 197, 94, 0.15);
  color: #86efac;
  padding: 12px;
  border-radius: 8px;
  margin-top: 10px;
  font-size: 14px;
}

.submit {
  background: linear-gradient(135deg, #f59e0b, #f97316);
  color: #111;
  font-weight: 800;
  border-radius: 999px;
  padding: 14px 30px;
  width: 100%;
}

@media (max-width: 900px) {
  .contact__content {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 700px) {
  .contact {
    padding: 25px 15px;
  }
  .contact__hero {
    padding: 35px 20px;
  }
  .contact__hero h1 {
    font-size: 28px;
  }
  .contact__info,
  .form-card {
    padding: 22px !important;
  }
}
</style>