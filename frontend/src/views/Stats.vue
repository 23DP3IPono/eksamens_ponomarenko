<template>
  <v-container class="stats" fluid>
    <section class="hero">
      <h1>📊 Statistika</h1>
      <p>Kopējs skatījums uz visiem ceļojumiem sistēmā.</p>
    </section>

    <Loader v-if="loading" text="Aprēķina statistiku..." />
    <div v-else-if="error" class="state error">⚠️ Neizdevās ielādēt statistiku.</div>

    <template v-else-if="data">
      <!-- Summary cards -->
      <section class="summary">
        <div class="summary__card">
          <div class="summary__num">{{ data.total_trips }}</div>
          <div class="summary__label">Ceļojumi kopā</div>
        </div>
        <div class="summary__card">
          <div class="summary__num">€ {{ formatMoney(data.total_budget) }}</div>
          <div class="summary__label">Kopējais budžets</div>
        </div>
        <div class="summary__card">
          <div class="summary__num">€ {{ formatMoney(data.avg_budget) }}</div>
          <div class="summary__label">Vidējais budžets</div>
        </div>
        <div class="summary__card">
          <div class="summary__num">€ {{ formatMoney(data.total_reservations) }}</div>
          <div class="summary__label">Rezervācijas kopā</div>
        </div>
        <div class="summary__card">
          <div class="summary__num">€ {{ formatMoney(data.total_expenses) }}</div>
          <div class="summary__label">Izdevumi kopā</div>
        </div>
      </section>

      <!-- Charts row -->
      <div class="charts">
        <section class="chart-block">
          <h2>Ceļojumi pa galamērķiem</h2>
          <div v-if="!data.by_destination.length" class="empty">Nav datu.</div>
          <Bar v-else :data="destinationChartData" :options="barOptions" />
        </section>

        <section class="chart-block">
          <h2>Izdevumi pa kategorijām</h2>
          <div v-if="!data.expenses_by_category.length" class="empty">Nav datu.</div>
          <Pie v-else :data="expensesChartData" :options="pieOptions" />
        </section>
      </div>

      <!-- Data tables -->
      <div class="tables">
        <section class="table-block">
          <h2>Galamērķi detalizēti</h2>
          <table class="table">
            <thead>
              <tr>
                <th>Galamērķis</th>
                <th>Ceļojumu skaits</th>
                <th>Kopējais budžets</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="d in data.by_destination" :key="d.galamerkis">
                <td>{{ d.galamerkis }}</td>
                <td>{{ d.count }}</td>
                <td>€ {{ formatMoney(d.total_budget) }}</td>
              </tr>
            </tbody>
          </table>
        </section>

        <section class="table-block">
          <h2>Rezervāciju tipi</h2>
          <table class="table">
            <thead>
              <tr>
                <th>Tips</th>
                <th>Skaits</th>
                <th>Kopā €</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="r in data.reservations_by_type" :key="r.tips">
                <td>{{ r.tips }}</td>
                <td>{{ r.count }}</td>
                <td>€ {{ formatMoney(r.total) }}</td>
              </tr>
            </tbody>
          </table>
        </section>
      </div>
    </template>
  </v-container>
</template>

<script>
import api from "@/api";
import { Bar, Pie } from "vue-chartjs";
import Loader from "@/components/Loader.vue";
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  ArcElement,
} from "chart.js";

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  ArcElement
);

export default {
  components: { Bar, Pie, Loader },
  data() {
    return {
      data: null,
      loading: true,
      error: null,
    };
  },

  computed: {
    destinationChartData() {
      return {
        labels: this.data.by_destination.map((d) => d.galamerkis),
        datasets: [
          {
            label: "Ceļojumu skaits",
            data: this.data.by_destination.map((d) => d.count),
            backgroundColor: "#f59e0b",
            borderRadius: 8,
          },
        ],
      };
    },
    expensesChartData() {
      const palette = [
        "#f59e0b", "#f97316", "#ef4444", "#ec4899",
        "#8b5cf6", "#3b82f6", "#10b981", "#22c55e",
      ];
      return {
        labels: this.data.expenses_by_category.map((e) => e.kategorija),
        datasets: [
          {
            data: this.data.expenses_by_category.map((e) => Number(e.total)),
            backgroundColor: palette,
            borderWidth: 0,
          },
        ],
      };
    },
    barOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { labels: { color: "#fff" } },
        },
        scales: {
          x: { ticks: { color: "#fff" }, grid: { color: "rgba(255,255,255,0.1)" } },
          y: {
            beginAtZero: true,
            ticks: { color: "#fff", stepSize: 1 },
            grid: { color: "rgba(255,255,255,0.1)" },
          },
        },
      };
    },
    pieOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: "right", labels: { color: "#fff" } },
        },
      };
    },
  },

  async mounted() {
    try {
      this.data = await api.getTripStats();
    } catch (err) {
      this.error = err.message;
    } finally {
      this.loading = false;
    }
  },

  methods: {
    formatMoney(n) {
      return Number(n || 0).toFixed(2);
    },
  },
};
</script>

<style scoped>
.stats {
  padding: 40px 20px;
  color: white;
}

.hero {
  background: linear-gradient(135deg, #111827, #0f172a);
  padding: 50px;
  border-radius: 20px;
  text-align: center;
  margin-bottom: 25px;
}
.hero h1 {
  font-size: 40px;
  font-weight: 900;
}
.hero p {
  opacity: 0.8;
  margin-top: 8px;
}

.state {
  text-align: center;
  padding: 60px;
  color: #aaa;
}
.state.error {
  color: #ff6b6b;
}

.summary {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 15px;
  margin-bottom: 30px;
}
.summary__card {
  background: #0b0f1a;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 18px;
  padding: 25px;
  text-align: center;
}
.summary__num {
  font-size: 26px;
  font-weight: 900;
  color: #f59e0b;
}
.summary__label {
  margin-top: 6px;
  opacity: 0.7;
  font-size: 13px;
}

.charts {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 30px;
}
.chart-block {
  background: #0b0f1a;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 20px;
  padding: 25px;
  display: flex;
  flex-direction: column;
}
.chart-block canvas {
  max-height: 320px !important;
  width: 100% !important;
}
.chart-block h2 {
  font-size: 20px;
  font-weight: 800;
  margin-bottom: 15px;
}

.tables {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}
.table-block {
  background: #0b0f1a;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 20px;
  padding: 25px;
}
.table-block h2 {
  font-size: 20px;
  font-weight: 800;
  margin-bottom: 15px;
}
.table {
  width: 100%;
  border-collapse: collapse;
}
.table th,
.table td {
  text-align: left;
  padding: 10px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
.table th {
  opacity: 0.7;
  font-weight: 600;
}
.empty {
  opacity: 0.6;
  font-style: italic;
  padding: 20px;
  text-align: center;
}

@media (max-width: 1100px) {
  .summary {
    grid-template-columns: repeat(3, 1fr);
  }
  .charts,
  .tables {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 700px) {
  .stats {
    padding: 25px 15px;
  }
  .hero {
    padding: 30px 20px;
  }
  .hero h1 {
    font-size: 28px;
  }
  .summary {
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
  }
  .summary__card {
    padding: 18px;
  }
  .summary__num {
    font-size: 20px;
  }
  .chart-block,
  .table-block {
    padding: 18px;
  }
  .chart-block h2,
  .table-block h2 {
    font-size: 17px;
  }
  .table {
    font-size: 13px;
  }
  .table th, .table td {
    padding: 8px 6px;
  }
}
</style>