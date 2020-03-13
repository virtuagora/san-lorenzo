<script>
import { Line } from "vue-chartjs";

export default {
  extends: Line,
  props: ["theLabels", "theData"],
  data() {
    return {};
  },
  mounted: function() {
    // Overwriting base render method with actual data.
    this.renderChart(
      {
        labels: this.theLabels,
        datasets: this.theData
      },
      {
       scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
      }
    );
  }
};
</script>