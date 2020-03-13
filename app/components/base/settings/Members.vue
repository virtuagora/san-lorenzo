<template>
  <div>
    <nav class="level">
      <!-- Left side -->
      <div class="level-left">
        <div class="level-item">
          <p class="subtitle is-5">
            <strong>123</strong> users
          </p>
        </div>
        <div class="level-item">
          <div class="field has-addons">
            <p class="control">
              <input class="input" type="text" placeholder="Find a post">
            </p>
            <p class="control">
              <button class="button">
                Search
              </button>
            </p>
          </div>
        </div>
      </div>

      <!-- Right side -->
      <div class="level-right">
        <p class="level-item">
          <strong>All</strong>
        </p>
        <p class="level-item">
          <a>Published</a>
        </p>
        <p class="level-item">
          <a>Drafts</a>
        </p>
        <p class="level-item">
          <a>Deleted</a>
        </p>
        <p class="level-item">
          <a class="button is-success">New</a>
        </p>
      </div>
    </nav>

    <table class="table is-fullwidth">
      <thead>
        <tr>
          <th>Name and surname</th>
          <th>Tags</th>
          <th class="has-text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <figure class="image is-24x24 is-rounded is-inline-block">
              <img alt="Placeholder image" src="/assets/img/avatar.jpg">
            </figure> Guillermo Croppi</td>
          <td>
            <div class="tags">
              <span class="tag is-info">Holis</span>
              <span class="tag">Holis</span>
            </div>
          </td>
          <td class="has-text-right">
            <a @click.prevent="doNothing" class="has-text-danger">
              <i class="fas fa-times"></i>
            </a>
          </td>
        </tr>
        <tr>
          <td>
            <figure class="image is-24x24 is-rounded is-inline-block">
              <img alt="Placeholder image" src="/assets/img/avatar.jpg">
            </figure> Guillermo Croppi</td>
          <td>
            <div class="tags">
              <span class="tag is-info">Holis</span>
              <span class="tag">Holis</span>
            </div>
          </td>
          <td class="has-text-right">
            <a @click.prevent="doNothing" class="has-text-danger">
              <i class="fas fa-times"></i>
            </a>
          </td>
        </tr>
        <tr>
          <td>
            <figure class="image is-24x24 is-rounded is-inline-block">
              <img alt="Placeholder image" src="/assets/img/avatar.jpg">
            </figure> Guillermo Croppi</td>
          <td>
            <div class="tags">
              <span class="tag is-info">Holis</span>
              <span class="tag">Holis</span>
            </div>
          </td>
          <td class="has-text-right">
            <a @click.prevent="doNothing" class="has-text-danger">
              <i class="fas fa-times"></i>
            </a>
          </td>
        </tr>
        <tr>
          <td>
            <figure class="image is-24x24 is-rounded is-inline-block">
              <img alt="Placeholder image" src="/assets/img/avatar.jpg">
            </figure> Guillermo Croppi</td>
          <td>
            <div class="tags">
              <span class="tag is-info">Holis</span>
              <span class="tag">Holis</span>
            </div>
          </td>
          <td class="has-text-right">
            <a @click.prevent="doNothing" class="has-text-danger">
              <i class="fas fa-times"></i>
            </a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  methods: {
    doNothing: function() {
      console.log("Doing nothing");
    }
  }
};
</script>

<style lang="scss" scoped>
figure {
  vertical-align: bottom;
}
</style>

