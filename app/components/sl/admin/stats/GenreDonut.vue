<script>
import { Doughnut } from "vue-chartjs";

export default {
  extends: Doughnut,
  props: ["arr","arrtwo"],
  data() {
    return {};
  },
  mounted: function() {
    // Overwriting base render method with actual data.
    this.renderChart({
      labels: ['Masculino','Femenino'],
      datasets: [{
        data: [ 
          this.arrtwo ? this.arr['M'].votantes - this.arrtwo['M'].votantes : this.arr['M'] && this.arr['M'].votantes || 0,
          this.arrtwo ? this.arr['F'].votantes - this.arrtwo['F'].votantes : this.arr['F'] && this.arr['F'].votantes || 0 ],
        backgroundColor: ['#2d3047',"#63b995"]
      }]
    });
  }
};
</script>
