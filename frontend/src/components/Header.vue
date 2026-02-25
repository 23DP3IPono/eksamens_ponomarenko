<template>
  <header class="header">
    <div class="logo" @click="$router.push('/')">
      🌍 Ceļojumu plānotājs
    </div>

    <nav class="nav">
      <router-link to="/">Sākums</router-link>
      <router-link to="/services">Ceļojumi</router-link>
      <router-link to="/contact">Kontakti</router-link>
    </nav>
  </header>
</template>

<script>
export default {};
</script>

<style>
.header {
  position: sticky;
  top: 0;
  z-index: 1000;
  
  backdrop-filter: blur(12px);
  background: rgba(15, 23, 42, 0.85);
  border-bottom: 1px solid rgba(255,255,255,0.1);
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);

  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 50px;
  transition: background 0.3s ease, box-shadow 0.3s ease;
}

.header:hover {
  background: rgba(15, 23, 42, 0.95);
  box-shadow: 0 6px 20px rgba(0,0,0,0.25);
}

.logo {
  font-weight: 900;
  font-size: 22px;
  color: white;
  letter-spacing: 1px;
  cursor: pointer;
  transition: color 0.3s ease, transform 0.3s ease;
}

.logo:hover {
  color: #f59e0b;
  transform: scale(1.05);
}

.nav {
  display: flex;
  gap: 35px;
}

.nav a {
  color: rgba(255,255,255,0.85);
  text-decoration: none;
  font-weight: 600;
  position: relative;
  transition: 0.3s ease;
  padding: 5px 0;
}

.nav a:hover {
  color: #f59e0b;
}

.nav a.router-link-active {
  color: #f59e0b;
}

.nav a::after {
  content: "";
  position: absolute;
  bottom: -4px;
  left: 0;
  height: 2px;
  width: 0%;
  background: #f59e0b;
  border-radius: 2px;
  transition: 0.3s ease;
}

.nav a:hover::after,
.nav a.router-link-active::after {
  width: 100%;
}
</style>