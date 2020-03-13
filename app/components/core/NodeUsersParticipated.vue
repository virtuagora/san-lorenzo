<template>
    <div>
        <h6 class="subtitle is-6">Who has participated?</h6>
        <div class="list-of-avatars">
            <figure class="image is-64x64 is-rounded">
                <a href="#">
                    <img alt="Placeholder image" src="http://a.facdn.net/1491068482/zaqueo.gif">
                </a>
            </figure>
            <figure class="image is-64x64 is-rounded">
                <a href="#">
                    <img alt="Placeholder image" src="http://a.facdn.net/1440457300/stringfox.gif">
                </a>
            </figure>
            <figure class="image is-64x64 is-rounded">
                <a href="#">
                    <img alt="Placeholder image" src="http://a.facdn.net/1517423747/palmedo.gif">
                </a>
            </figure>
        </div>
    </div>
</template>

<script>
export default {
    props: ['users', 'size'],
    data(){
        return {
            
        }
    }
}
</script>
