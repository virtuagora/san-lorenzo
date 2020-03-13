<template>
  <section v-if="user != null">
    <div v-if="user.groups[0] !== undefined">
      <ul class="steps has-gaps">
        <li class="steps-segment">
          <router-link :to="{ name: 'userVerEquipo'}">
          <span class="steps-marker">
            <span class="icon">
              <i class="fas fa-check"></i>
            </span>
          </span>
          </router-link>
          <span class="steps-content">
            <p class="heading">Crear equipo</p>
            <p class="is-size-7">El responsable debe cargar todos los datos del equipo</p>
          </span>
        </li>
        <li class="steps-segment" :class="{'is-active': !user.groups[0].full_team}">
          <router-link :to="{ name: 'userVerIntegrantes'}">
          <span class="steps-marker" :class="{'is-primary': user.groups[0].full_team}">
            <span class="icon">
              <i class="fas" :class="{'fa-check': user.groups[0].full_team, 'fa-clock': !user.groups[0].full_team}"></i>
            </span>
          </span>
          </router-link>
          <span class="steps-content">
            <p class="heading">Cupo mínimo</p>
            <p class="is-size-7">El equipo debe contar con 5 miembros como mínimo</p>
          </span>
        </li>
        <li class="steps-segment" :class="{'is-active': !user.groups[0].second_in_charge}">
          <router-link :to="{ name: 'userVerIntegrantes'}">          
          <span class="steps-marker" :class="{'is-primary': user.groups[0].second_in_charge}">
            <span class="icon">
              <i class="fas" :class="{'fa-check': user.groups[0].second_in_charge, 'fa-clock': !user.groups[0].second_in_charge}"></i>
            </span>
          </span>
          </router-link>
          <span class="steps-content">
            <p class="heading">Co-responsable</p>
            <p class="is-size-7">Deben asignar un co-responsable</p>            
          </span>
        </li>
        <li class="steps-segment is-dashed" :class="{'is-active': !user.groups[0].uploaded_agreement}">
          <router-link :to="{ name: 'userSubirConformidad'}">
          <span class="steps-marker" :class="{'is-primary': user.groups[0].uploaded_agreement}">
            <span class="icon">
              <i class="fas" :class="{'fa-check': user.groups[0].uploaded_agreement, 'fa-clock': !user.groups[0].uploaded_agreement}"></i>
            </span>
          </span>
          </router-link>
          <span class="steps-content">
            <p class="heading">Carta de conformidad</p>
            <p class="is-size-7">Todo el equipo debe firmar la carta de conformidad y subirla a la web</p>                        
          </span>
        </li>
        <li class="steps-segment" :class="{'is-active': user.groups[0].uploaded_agreement && user.groups[0].second_in_charge && user.groups[0].uploaded_agreement}">
          <span class="steps-marker" :class="{'is-success': user.groups[0].uploaded_agreement && user.groups[0].second_in_charge && user.groups[0].uploaded_agreement}">
            <span class="icon">
              <i class="fas fa-flag-checkered"></i>
            </span>
          </span>
          <span class="steps-content">
            <p class="heading" v-show="user.groups[0].uploaded_agreement && user.groups[0].second_in_charge && user.groups[0].uploaded_agreement">¡OK!</p>
          </span>
        </li>
      </ul>
      <br>
      <ul class="steps has-gaps" v-if="user.groups[0].project === null">
        <li class="steps-segment is-active">
          <router-link :to="{ name: 'userEditarProyecto'}">
          <span class="steps-marker">
            <span class="icon">
              <i class="fas fa-clock"></i>
            </span>
          </span>
          </router-link>
          <span class="steps-content">
            <p class="heading">Cargar proyecto</p>
            <p class="is-size-7">El responsable debe cargar todos los datos del proyecto</p>
          </span>
        </li>
        <li class="steps-segment">
          <span class="steps-marker">
            <span class="icon">
              <i class="fas fa-flag-checkered"></i>
            </span>
          </span>
          <span class="steps-content">
            <p class="heading">¡OK!</p>
          </span>
        </li>
      </ul>
      <ul class="steps has-gaps" v-else>
        <li class="steps-segment" :class="{'is-active': !user.groups[0].project === null, 'is-dashed': user.groups[0].project.organization === null }">
          <router-link :to="{ name: 'userEditarProyecto'}">
          <span class="steps-marker">
            <span class="icon">
              <i class="fas" :class="{'fa-check': user.groups[0].project !== null, 'fa-clock': !user.groups[0].project === null}"></i>
            </span>
          </span>
          </router-link>
          <span class="steps-content">
            <p class="heading">Cargar proyecto</p>
            <p class="is-size-7">El responsable debe cargar todos los datos del proyecto</p>
          </span>
        </li>
        <li class="steps-segment is-dashed" :class="{'is-active': user.groups[0].project.organization !== null && !user.groups[0].uploaded_letter, 'is-hidden': user.groups[0].project.organization === null }">
          <router-link :to="{ name: 'userSubirAvalOrganizacion'}">
          <span class="steps-marker">
            <span class="icon">
              <i class="fas" :class="{'fa-check': user.groups[0].uploaded_letter, 'fa-clock': !user.groups[0].uploaded_letter}"></i>
            </span>
          </span>
          </router-link>
          <span class="steps-content">
            <p class="heading">Aval organización</p>
            <p class="is-size-7">El proyecto se realiza con una organización, deben subir el aval del mismo</p>
          </span>
        </li>
        <li class="steps-segment is-dashed" :class="{'is-active': user.groups[0].project && (user.groups[0].project.organization === null || (user.groups[0].project.organization !== null && user.groups[0].uploaded_letter))}">
          <span class="steps-marker" :class="{'is-success': user.groups[0].project && (user.groups[0].project.organization === null || (user.groups[0].project.organization !== null && user.groups[0].uploaded_letter))}">
            <span class="icon">
              <i class="fas fa-flag-checkered"></i>
            </span>
          </span>
          <span class="steps-content">
            <p class="heading" v-show="user.groups[0].project && (user.groups[0].project.organization === null || (user.groups[0].project.organization !== null && user.groups[0].uploaded_letter))">¡OK!</p>
          </span>
        </li>
      </ul>
       <div class="notification is-success" v-if="user.groups[0].full_team && user.groups[0].second_in_charge && user.groups[0].uploaded_agreement && user.groups[0].project !== null && (user.groups[0].project.organization === null || (user.groups[0].project.organization !== null && user.groups[0].uploaded_letter))">
        <i class="fas fa-check fa-fw"></i> <b>¡FELICIDADES!</b> ¡Tu equipo y proyecto se encuentra ya participando de ingenia!
      </div>
      <div class="notification is-light" v-else>
        <i class="fa far fa-hand-point-up fa-fw fa-lg"></i><i class="fas fa-clock fa-fw"></i> Aún le quedan pasos pendientes por completar al equipo para estar adentro de INGENIA
      </div>
    </div>
    <div v-else>
      <ul class="steps has-gaps ">
        <li class="steps-segment is-dashed">
          <router-link :to="{ name: 'userEditarPerfil'}">
            <span class="steps-marker is-hollow">
            </span>
          </router-link>
          <span class="steps-content">
            <p class="heading">Crear cuenta</p>
            <p class="is-size-7">¡Bienvenido! Completá tu perfil de usuario (Opcional)</p>
          </span>
        </li>
        <!-- <li class="steps-segment" :class="{'is-active': user.pending_tasks.includes('email')}">
          <router-link :to="{ name: 'userEditarEmail'}">
            <span class="steps-marker" :class="{'is-primary': !user.pending_tasks.includes('email')}">
              <span class="icon">
                <i class="fas" :class="{'fa-check': !user.pending_tasks.includes('email'), 'fa-clock': user.pending_tasks.includes('email')}"></i>
              </span>
            </span>
          </router-link>
          <span class="steps-content">
            <p class="heading">Verificar email</p>
            <p class="is-size-7">Solo responsables del proyecto deben verificar su cuenta de email</p>
          </span>
        </li> -->
        <li class="steps-segment" :class="{'is-active': user.pending_tasks.includes('profile')}">
          <router-link :to="{ name: 'userEditarMisDatosPersonales'}">
            <span class="steps-marker" :class="{'is-primary': !user.pending_tasks.includes('profile')}">
              <span class="icon">
                <i class="fas" :class="{'fa-check': !user.pending_tasks.includes('profile'), 'fa-clock': user.pending_tasks.includes('profile')}"></i>
              </span>
            </span>
          </router-link>
          <span class="steps-content">
            <p class="heading">Datos Personales</p>
            <p class="is-size-7">Requerido para participar de INGENIA</p>
          </span>
        </li>
        <li class="steps-segment" :class="{'is-active': user.pending_tasks.includes('dni')}">
          <router-link :to="{ name: 'userSubirDNI'}">
            <span class="steps-marker" :class="{'is-primary': !user.pending_tasks.includes('dni')}">
              <span class="icon">
                <i class="fas" :class="{'fa-check': !user.pending_tasks.includes('dni'), 'fa-clock': user.pending_tasks.includes('dni')}"></i>
              </span>
            </span>
          </router-link>
          <span class="steps-content">
            <p class="heading">Cargar DNI</p>
            <p class="is-size-7">Requerido para participar de INGENIA</p>
          </span>
        </li>
        <li class="steps-segment" :class="{'is-active': !user.pending_tasks.includes('email') && !user.pending_tasks.includes('profile') && !user.pending_tasks.includes('dni')}">
          <span class="steps-marker" :class="{'is-success': !user.pending_tasks.includes('email') && !user.pending_tasks.includes('profile') && !user.pending_tasks.includes('dni')}">
            <span class="icon">
              <i class="fas fa-flag-checkered"></i>
            </span>
          </span>
          <span class="steps-content">
            <p class="heading" v-show="!user.pending_tasks.includes('email') && !user.pending_tasks.includes('profile') && !user.pending_tasks.includes('dni')">¡OK!</p>
            <p class="is-size-7"></p>
          </span>
        </li>
      </ul>
      <div class="notification is-success" v-if="!user.pending_tasks.includes('email') && !user.pending_tasks.includes('profile') && !user.pending_tasks.includes('dni')">
        <i class="fas fa-check fa-fw"></i> ¡Ya te encontrás en condiciones para participar en un equipo INGENIA o presentando un proyecto!
      </div>
      <div class="notification is-light" v-else>
        <i class="fa far fa-hand-point-up fa-fw fa-lg"></i><i class="fas fa-clock fa-fw"></i> Aún te quedan pasos pendientes por completar para poder participar de INGENIA
      </div>
    </div>
  </section>
  <section v-else>
    <div class="notification">
      <br>
        <b-loading :is-full-page="false" :active.sync="isLoading"></b-loading>
      <br>
    </div>
  </section>
</template>

<script>
export default {
  data() {
    return {
      user: {},
      isLoading: false
    };
  },
  created: function() {
      this.user = this.$store.state.user;
      this.isLoading = true

      let intervalId = setInterval(
      function() {
        if (this.user == null) {
        console.log("Getting user!");
          this.user = this.$store.state.user;
        } else {
        console.log("Got the user!");          
          clearInterval( intervalId );
        }
      }.bind(this),
      2000
    );
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      if (
        vm.user.groups[0] !== undefined &&
        vm.user.groups[0].pivot.relation === "responsable"
      ) {
        console.log("Authorized");
      } else {
        console.log("Unauthorized - Kicking to dashboard!");
        next({ name: "panelOverview" });
      }
    });
  }
};
</script>
