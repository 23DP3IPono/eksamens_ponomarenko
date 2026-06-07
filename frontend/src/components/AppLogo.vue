<template>
  <div class="app-logo" :style="{ height: size + 'px' }">
    <svg :width="size" :height="size" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
      <!-- Globe base -->
      <circle cx="32" cy="32" r="30" fill="#0a1628"/>
      <!-- Grid lines latitude -->
      <ellipse cx="32" cy="32" rx="30" ry="9" fill="none" stroke="#1a3060" stroke-width="0.8" opacity="0.7"/>
      <ellipse cx="32" cy="32" rx="30" ry="19" fill="none" stroke="#1a3060" stroke-width="0.7" opacity="0.6"/>
      <!-- Grid lines longitude -->
      <ellipse cx="32" cy="32" rx="16" ry="30" fill="none" stroke="#1a3060" stroke-width="0.7" opacity="0.5"/>
      <line x1="32" y1="2" x2="32" y2="62" stroke="#1a3060" stroke-width="0.7" opacity="0.5"/>
      <!-- Continents -->
      <path d="M25,13 L32,11 L38,16 L36,24 L29,26 L23,21 Z" fill="#1e4020" opacity="0.95"/>
      <path d="M22,27 L29,25 L33,33 L29,42 L22,42 L17,36 Z" fill="#1e4020" opacity="0.9"/>
      <path d="M39,16 L50,14 L55,21 L51,30 L40,31 L36,23 Z" fill="#1e4020" opacity="0.85"/>
      <path d="M44,38 L52,36 L56,43 L52,51 L44,52 L39,46 Z" fill="#1e4020" opacity="0.8"/>
      <!-- Globe ring -->
      <circle cx="32" cy="32" r="30" fill="none" stroke="#F5A623" stroke-width="2.5"/>
      <!-- Flight arc glow -->
      <path d="M18,46 Q32,8 50,14" fill="none" stroke="#F5A623" stroke-width="7" stroke-linecap="round" opacity="0.12"/>
      <!-- Flight arc -->
      <path d="M18,46 Q32,8 50,14" fill="none" stroke="#F5A623" stroke-width="2.5" stroke-linecap="round"/>
      <!-- Departure pin -->
      <circle cx="18" cy="46" r="4" fill="#F5A623"/>
      <circle cx="18" cy="46" r="7.5" fill="none" stroke="#F5A623" stroke-width="1.2" opacity="0.4"/>
      <!-- Airplane -->
      <g transform="translate(50,14) rotate(32)">
        <ellipse cx="0" cy="0" rx="3" ry="10" fill="#ffffff"/>
        <ellipse cx="0" cy="1.5" rx="13" ry="2.8" fill="#ffffff"/>
        <ellipse cx="0" cy="7.5" rx="5" ry="1.8" fill="#ffffff"/>
        <circle cx="0" cy="-4" r="2.5" fill="#F5A623"/>
      </g>
    </svg>

    <div v-if="showText" class="logo-text">
      <span class="logo-main">Ceļojumu</span>
      <span class="logo-sub">plānotājs</span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  size: {
    type: Number,
    default: 40
  },
  showText: {
    type: Boolean,
    default: true
  }
})
</script>

<style scoped>
.app-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  cursor: pointer;
}

.logo-text {
  display: flex;
  flex-direction: column;
  line-height: 1.1;
}

.logo-main {
  font-family: 'DM Sans', sans-serif;
  font-weight: 700;
  font-size: 17px;
  color: #ffffff;
  letter-spacing: -0.2px;
}

.logo-sub {
  font-family: 'DM Sans', sans-serif;
  font-weight: 700;
  font-size: 17px;
  color: #F5A623;
  letter-spacing: -0.2px;
}
</style>
