<!-- FavoriteButton.vue -->
<template>
  <button
    v-if="auth.isLoggedIn"
    class="fav-btn"
    :class="{ 'fav-btn--on': isFav }"
    :title="isFav ? 'Noņemt no iecienītajiem' : 'Pievienot iecienītajiem'"
    :disabled="busy"
    @click.stop="toggle"
  >
    {{ isFav ? "⭐" : "☆" }}
  </button>
</template>

<script>
import api from "@/api";
import { useAuthStore } from "@/stores/auth";

export default {
  props: {
    tripId: { type: [Number, String], required: true },
    initial: { type: Boolean, default: null },
  },
  emits: ["change"],
  data() {
    return {
      isFav: false,
      busy: false,
      loaded: false,
    };
  },
  computed: {
    auth() {
      return useAuthStore();
    },
  },
  async mounted() {
    if (!this.auth.isLoggedIn) return;

    if (this.initial !== null) {
      this.isFav = this.initial;
      this.loaded = true;
      return;
    }

    try {
      const res = await api.checkFavorite(this.tripId);
      this.isFav = !!res.favorited;
    } catch (_) {
      // ignore
    } finally {
      this.loaded = true;
    }
  },
  methods: {
    async toggle() {
      if (this.busy) return;
      this.busy = true;
      try {
        if (this.isFav) {
          await api.removeFavorite(this.tripId);
          this.isFav = false;
        } else {
          await api.addFavorite(this.tripId);
          this.isFav = true;
        }
        this.$emit("change", this.isFav);
      } catch (err) {
        alert("Kļūda: " + err.message);
      } finally {
        this.busy = false;
      }
    },
  },
};
</script>

<style scoped>
.fav-btn {
  background: rgba(0, 0, 0, 0.55);
  border: 1px solid rgba(255, 255, 255, 0.15);
  color: #fff;
  width: 42px;
  height: 42px;
  border-radius: 50%;
  font-size: 20px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.fav-btn:hover {
  transform: scale(1.1);
  border-color: #f59e0b;
}
.fav-btn--on {
  color: #fbbf24;
  border-color: #f59e0b;
  box-shadow: 0 0 12px rgba(251, 191, 36, 0.5);
}
.fav-btn:disabled {
  opacity: 0.6;
  cursor: wait;
}
</style>