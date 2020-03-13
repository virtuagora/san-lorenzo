<script>
import { HorizontalBar } from "vue-chartjs";

export default {
  extends: HorizontalBar,
  props: ["labels", "web","tablet","link","paper", "total"],
  data() {
    return {
      options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
          xAxes: [{
            ticks: {
              suggestedMin: 0
            }
          }]
        }
      }
    };
  },
  mounted: function() {
    // Overwriting base render method with actual data.
    this.renderChart({
      labels: this.correctLabels,
      datasets: this.datasets
    },
    this.options);
  },
  computed: {
    datasets: function() {
      let dataset = [];
      dataset.push({
        label: "Web",
        backgroundColor: "#caa8f5",
        data: this.correctLabels.map(date => {
          return this.web[date] && this.web[date].votes || 0;
        })
      });
      dataset.push({
        label: "Tablet",
        backgroundColor: "#9984d4",
        data: this.correctLabels.map(date => {
          return this.tablet[date] && this.tablet[date].votes || 0;
        })
      });
      dataset.push({
        label: "Link",
        backgroundColor: "#592e83",
        data: this.correctLabels.map(date => {
          return this.link[date] && this.link[date].votes || 0;
        })
      });
      dataset.push({
        label: "Papel",
        backgroundColor: "#230c33",
        data: this.correctLabels.map(date => {
          return this.paper[date] && this.paper[date].votes || 0;
        })
      });
      dataset.push({
        label: "Total",
        backgroundColor: "#519872",
        data: this.correctLabels.map(date => {
          return this.total[date] && this.total[date].votes || 0 ;
        })
      });
      return dataset
    },
    correctLabels: function() {
      // return this.labels.filter(date => {
      //   if (this.total[date]) return true;
      // });
      return this.labels
    }
  }
};
</script>
