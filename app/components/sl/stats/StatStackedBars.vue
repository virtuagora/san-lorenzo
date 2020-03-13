<script>
import { Bar } from "vue-chartjs";

export default {
  extends: Bar,
  props: ["theLabels", "theData"],
  data() {
    return {
    };
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
          xAxes: [{stacked:true}],
          yAxes: [
            {
              stacked: true,
              ticks: {
                beginAtZero: true,
              }
            }
          ]
        }
      }
    );
  }
};
</script>
