import { createRouter, createWebHistory } from "vue-router";
import Home from "../views/Home.vue";
import Services from "../views/Services.vue";
import Contact from "../views/Contact.vue";
import TripDetail from "../views/TripDetail.vue";
import TripForm from "../views/TripForm.vue";
import MyTrips from "../views/MyTrips.vue";
import Login from "../views/Login.vue";
import Register from "../views/Register.vue";
import Stats from "../views/Stats.vue";

const routes = [
  { path: "/", component: Home },
  { path: "/services", component: Services },
  { path: "/services/new", component: TripForm },
  { path: "/services/:id", component: TripDetail },
  { path: "/services/:id/edit", component: TripForm },
  { path: "/my-trips", component: MyTrips },
  { path: "/contact", component: Contact },
  { path: "/login", component: Login },
  { path: "/register", component: Register },
  { path: "/stats", component: Stats },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;