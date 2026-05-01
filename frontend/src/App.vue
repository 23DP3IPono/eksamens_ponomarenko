<template>
  <div class="app">
    <Header />
    <main class="app__main">
      <router-view />
    </main>
    <Footer />
  </div>
</template>

<script>
import Header from "./components/Header.vue";
import Footer from "./components/Footer.vue";

export default {
  components: { Header, Footer },
};
</script>

<style>
.app {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background: #0a0e1a;
}
.app__main {
  flex: 1;
}

.v-field {
  color: white !important;
}
.v-field input,
.v-field textarea {
  color: white !important;
}
.v-field__outline {
  --v-field-border-opacity: 0.3 !important;
}
.v-label {
  color: rgba(255, 255, 255, 0.7) !important;
}

@media (max-width: 700px) {
  .v-container {
    padding-left: 13px !important;
    padding-right: 13px !important;
  }
}
</style>