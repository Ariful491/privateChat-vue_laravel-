<template>

   <div id="main-app">
            <chatComponent/>
   </div>

</template>

<script>
    import chatComponent from './ChatApp';


    export default {
        name: "MainApp",
        components:{
              chatComponent
              }
    }
</script>

<style >

</style>
