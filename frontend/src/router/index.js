import { createRouter, createWebHistory } from "vue-router";
import Home from "../views/Home.vue";
import Services from "../views/Services.vue";
import Contact from "../views/Contact.vue";
import TripDetail from "../views/TripDetail.vue";
import TripForm from "../views/TripForm.vue";
import MyTrips from "../views/MyTrips.vue";
import Stats from "../views/Stats.vue";
import Login from "../views/Login.vue";
import Register from "../views/Register.vue";
import Admin from "../views/Admin.vue";

const routes = [
  { path: "/", component: Home },
  { path: "/services", component: Services },
  { path: "/services/new", component: TripForm },
  { path: "/services/:id", component: TripDetail },
  { path: "/services/:id/edit", component: TripForm },
  { path: "/my-trips", component: MyTrips },
  { path: "/stats", component: Stats },
  { path: "/contact", component: Contact },
  { path: "/login", component: Login },
  { path: "/register", component: Register },
  { path: "/admin", component: Admin },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition;
    return { top: 0 };
  },
});

export default router;