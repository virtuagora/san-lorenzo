<template>
<div>
  <team-avatars :id="project.group.id" :team-url="teamUrl" :get-group-members="getGroupMembers"></team-avatars>
  <coordinators 
   :granted-budget="grantedBudget == '' ? null : grantedBudget"
   :selected=" selected == '' ? null : selected" 
   :quota="quota == '' ? null : quota" 
   :is-admin="isAdmin ? true : false" 
   :is-coordinator="isCoordinator ? true : null" 
   :id-coordinator="idCoordinator ? idCoordinator : null"
   :id="project.id"
   :update-review="updateReview"
   :assign-coordinator="assignCoordinator"
   :delete-coordinator="deleteCoordinator"
   v-if="isAdmin || isCoordinator"></coordinators>
  <router-view :put-project-note="putProjectNote" :selected=" selected == '' ? null : selected" :is-admin="isAdmin ? true : false" :is-coordinator="isCoordinator ? true : null" :id-coordinator="idCoordinator ? idCoordinator : null" :notes="notes ? notes : null" :project="project" :send-request-join="sendRequestJoin"></router-view>
      <hr class="is-marginless">
  <section class="section" id="comentarios">
      <div class="container">
        <div class="columns">
          <div class="column is-8 is-offset-2"> 
            <comments :comments-url="commentsUrl" :comment-url="commentUrl" :reply-url="replyUrl" :like-url="likeUrl"></comments>
          </div>
        </div>
      </div>
    </section>
</div>
</template>

<script>
import TeamAvatars from './TeamAvatars'
import Coordinators from './Coordinators'
import Comments from './Comments'
export default {
  props:['project','isAdmin','notes','grantedBudget','selected','quota','isCoordinator',
  'putProjectNote','idCoordinator', 'updateReview', 'assignCoordinator','deleteCoordinator',
  "getGroupMembers", 'sendRequestJoin', 'teamUrl', 'commentsUrl', 'commentUrl','replyUrl','likeUrl'],
  components:{
    TeamAvatars,
    Coordinators,
    Comments
  },
  methods: {
    
  }
}
</script>

<style>

</style>
