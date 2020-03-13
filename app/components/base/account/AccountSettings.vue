  <template>
  <div class="columns">
    <div class="column is-3">
      <aside class="menu">
        <p class="menu-label">
          Profile
        </p>
        <ul class="menu-list">
          <li>
            <router-link :to="{ name: 'accountProfile'}" exact-active-class="is-active" exact>Overview</router-link>
          </li>
         <li>
            <router-link :to="{ name: 'accountEditProfile'}" exact-active-class="is-active">Edit profile</router-link>
          </li>
        </ul>
        <p class="menu-label">
          Account Settings
        </p>
        <ul class="menu-list">
         <li>
            <router-link :to="{ name: 'accountPassword'}" exact-active-class="is-active">Change my password</router-link>
          </li>
        </ul>
      </aside>
    </div>
    <div class="column is-5 is-offset-2">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
export default {
  props: ['id'],
  data(){
    return {

    }
  },
  beforeCreate: function(){
    
  }
};
</script>
