<!-- App.vue -->
<template>
  <div class="app">
    <Header />
    <main class="app__main">
      <transition name="fade" mode="out-in">
        <router-view />
      </transition>
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
}
.app__main {
  flex: 1;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.35s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>