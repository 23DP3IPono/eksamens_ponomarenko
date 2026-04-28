<template>
  <footer class="footer">
    <div class="footer__content">
      <div class="footer__section">
        <h3>🌍 Ceļojumu plānotājs</h3>
        <p>
          Plāno savus sapņu ceļojumus vienuviet. Organizē galamērķus, budžetu
          un aktivitātes ērti un ātri.
        </p>
      </div>

      <div class="footer__section">
        <h4>Navigācija</h4>
        <ul>
          <li><router-link to="/">Sākums</router-link></li>
          <li><router-link to="/services">Ceļojumi</router-link></li>
          <li><router-link to="/stats">Statistika</router-link></li>
          <li><router-link to="/contact">Kontakti</router-link></li>
        </ul>
      </div>

      <div class="footer__section">
        <h4>Par mums</h4>
        <p>
          Šī sistēma ir izstrādāta kā noslēguma darbs Rīgas Valsts tehnikuma
          Programmēšanas izglītības programmā.
        </p>
      </div>

      <div class="footer__section">
        <h4>Seko mums</h4>
        <div class="footer__socials">
          <a href="#" aria-label="Instagram">📷</a>
          <a href="#" aria-label="Facebook">👍</a>
          <a href="#" aria-label="Twitter">🐦</a>
          <a href="#" aria-label="YouTube">▶️</a>
        </div>
      </div>
    </div>

    <div class="footer__bottom">
      © 2026 Ceļojumu plānotājs. Visas tiesības aizsargātas.
    </div>
  </footer>
</template>

<script>
export default {};
</script>

<style scoped>
.footer {
  background: #05070d;
  color: #aaa;
  padding: 50px 50px 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
}
.footer__content {
  display: grid;
  grid-template-columns: 2fr 1fr 1.5fr 1fr;
  gap: 40px;
  max-width: 1300px;
  margin: 0 auto;
  padding-bottom: 30px;
}
.footer__section h3 {
  color: white;
  font-weight: 900;
  font-size: 20px;
  margin-bottom: 12px;
}
.footer__section h4 {
  color: white;
  font-weight: 700;
  font-size: 16px;
  margin-bottom: 12px;
}
.footer__section p {
  font-size: 14px;
  line-height: 1.6;
}
.footer__section ul {
  list-style: none;
  padding: 0;
}
.footer__section li {
  margin-bottom: 8px;
}
.footer__section a {
  color: #aaa;
  text-decoration: none;
  font-size: 14px;
  transition: color 0.2s;
}
.footer__section a:hover {
  color: #f59e0b;
}
.footer__socials {
  display: flex;
  gap: 12px;
  font-size: 22px;
}
.footer__socials a {
  width: 40px;
  height: 40px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  transition: background 0.2s, transform 0.2s;
}
.footer__socials a:hover {
  background: rgba(245, 158, 11, 0.15);
  transform: translateY(-3px);
}
.footer__bottom {
  text-align: center;
  padding-top: 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  font-size: 13px;
  opacity: 0.65;
}
@media (max-width: 1000px) {
  .footer__content {
    grid-template-columns: 1fr 1fr;
  }
}
@media (max-width: 600px) {
  .footer__content {
    grid-template-columns: 1fr;
  }
  .footer {
    padding: 40px 20px 15px;
  }
}
</style>