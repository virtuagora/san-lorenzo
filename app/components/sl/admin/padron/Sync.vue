<template>
  <section>
    <search
      ref="search"
      v-show="!showVotingMethod"
      :url="url"
      :urlAddCitizen="urlAddCitizen"
      :votable="votable"
      @chooseVoteMethod="chooseVoteMethod"
    ></search>
    <vote-method
      ref="voteMethod"
      v-if="showVotingMethod"
      :citizen="citizen"
      :url="url"
      :votable="votable"
      :urlParticipacion="urlParticipacion"
      :urlPublicVote="urlPublicVote"
      @closeVotingMethods="showVotingMethod = false"
      @resetAll="resetAll"
    ></vote-method>
  </section>
</template>

<script>

import Search from "./Search";
import VoteMethod from "./VoteMethod";

export default {
  props: ["url", "urlAddCitizen", "urlParticipacion", "votable","urlPublicVote"],
  data() {
    return {
      showVotingMethod: false,
      citizen: null
    };
  },
  components: {
    Search,
    VoteMethod
  },
  methods: {
    resetAll: function() {
      this.showVotingMethod = false;
      this.citizen = null
      this.$refs.search.resetAll()
    },
    chooseVoteMethod: function(citizen) {
      this.citizen = citizen;
      this.showVotingMethod = true;
    }
  },
  computed: {
  }
};
</script>
