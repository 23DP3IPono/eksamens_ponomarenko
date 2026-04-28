<template>
  <div class="loader">
    <div class="loader__spinner"></div>
    <div class="loader__text">{{ text }}</div>
  </div>
</template>

<script>
export default {
  props: {
    text: {
      type: String,
      default: "Ielādē...",
    },
  },
};
</script>

<style scoped>
.loader {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  gap: 16px;
}
.loader__spinner {
  width: 48px;
  height: 48px;
  border: 4px solid rgba(245, 158, 11, 0.2);
  border-top-color: #f59e0b;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
.loader__text {
  color: #aaa;
  font-size: 15px;
  letter-spacing: 0.3px;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>