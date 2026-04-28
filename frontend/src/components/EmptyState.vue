<template>
  <div class="empty-state">
    <div class="empty-state__icon">{{ icon }}</div>
    <h3 class="empty-state__title">{{ title }}</h3>
    <p v-if="subtitle" class="empty-state__subtitle">{{ subtitle }}</p>
    <v-btn
      v-if="actionText"
      class="empty-state__btn"
      @click="$emit('action')"
    >
      {{ actionText }}
    </v-btn>
  </div>
</template>

<script>
export default {
  props: {
    icon: { type: String, default: "📭" },
    title: { type: String, required: true },
    subtitle: { type: String, default: "" },
    actionText: { type: String, default: "" },
  },
  emits: ["action"],
};
</script>

<style scoped>
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
  gap: 10px;
}
.empty-state__icon {
  font-size: 64px;
  margin-bottom: 10px;
  filter: grayscale(0.2);
  opacity: 0.85;
}
.empty-state__title {
  font-size: 22px;
  font-weight: 800;
  color: white;
}
.empty-state__subtitle {
  color: #aaa;
  max-width: 380px;
}
.empty-state__btn {
  margin-top: 18px;
  background: linear-gradient(135deg, #f59e0b, #f97316);
  color: #111;
  font-weight: 800;
  border-radius: 999px;
  padding: 12px 28px;
}
</style>