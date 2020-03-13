<template>
  <div class="columns">
    <div class="column is-3">
      <aside class="menu">
        <p class="menu-label">
          Settings
        </p>
        <ul class="menu-list">
          <li>
            <router-link :to="{ name: 'settingsOverview'}" active-class="is-active" >Overview</router-link>
          </li>
         <li>
            <router-link :to="{ name: 'settingsModeration'}" active-class="is-active">Moderation</router-link>
          </li>
          <li >
            <router-link :to="{ name: 'settingsMembers'}" active-class="is-active">Members</router-link>
          </li>
        </ul>
      </aside>
    </div>
    <div class="column">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
export default {};
</script>
