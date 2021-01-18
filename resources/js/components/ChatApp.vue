<template>
    <div class="container-chat clearfix">
        <div class="people-list" id="people-list">
            <div class="search">
                <input type="text" placeholder="search" />
                <i class="fa fa-search"></i>
            </div>
            <ul class="list">


                <li @click.prevent="selectUser(user.id)" class="clearfix" v-for="user in userList" :key="user.id">

                    <div class="about">
                        <div class="name">{{user.name}}</div>
                        <div class="status" style="color:#fff;">
						<div v-if="onlineUser(user.id) || online.id == user.id" >
                            <i  class="fa fa-circle online" style="color:red;"></i> online
						</div>
						<div v-else >
                            <i class="fa fa-circle " style="color:#fff;"></i> offline
							</div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="chat">
            <div class="chat-header clearfix">


                <div class="chat-about">
                    <div v-if="userMessage.user"  class="chat-with"> <div class="dropdown"><i style="color:red;" class="fa fa-circle online"></i>   {{userMessage.user.name}}
								  <button class="btn dropdown-toggle dropdown-toggle-split" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									
								  </button>

								  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
									<a @click.prevent="deleteAllMessage" class="dropdown-item" href="#">Delete All message</a>
									
									
								  </div>
								</div> </div>
                   
                </div>
                <i class="fa fa-star"></i>
            </div> <!-- end chat-header -->

            <div v-chat-scroll class="chat-history">
                <ul v-for="message in userMessage.messages" :key="message.id">
                    <li v-if="userMessage.user.id !== message.user.id" class="clearfix" >
                        <div class="message-data align-right">
                            <span class="message-data-time" >{{message.created_at | timeformat}}</span> &nbsp; &nbsp;
                            <span class="message-data-name" > 	<div class="dropdown"><i class="fa fa-circle online" style="color:red;"></i>  {{message.user.name}}
								  <button class="btn dropdown-toggle dropdown-toggle-split" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									 
								  </button>

								  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
									<a @click.prevent="deleteSingleMessage(message.id)" class="dropdown-item" href="#">Delete message</a>
									
									
								  </div>
								</div> </span> 
							
						
							

                        </div>
                        <div class="message other-message float-right">
                            {{message.message}}
                        </div>
						
						
						
                    </li>

                  <li v-if="userMessage.user.id === message.user.id">
                        <div class="message-data">
                            <span class="message-data-name">
							
							<div class="dropdown"><i class="fa fa-circle online"></i>  {{message.user.name}}
								  <button class="btn dropdown-toggle dropdown-toggle-split" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									
								  </button>

								  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
									<a @click.prevent="deleteSingleMessage(message.id)" class="dropdown-item" href="#">Delete message</a>
									
									
								  </div>
								</div> 
							
							</span>
                            <span class="message-data-time">{{message.created_at | timeformat}}</span>
                        </div>
                        <div class="message my-message">
                            {{message.message}}
                        </div>
				   </li>
                    

                    

                </ul>

            </div> <!-- end chat-history -->

            <div class="chat-message clearfix">
			<p v-if="typing">{{typing}} typing................</p>
                <textarea v-if="userMessage.user"   @keydown="typingEvent(userMessage.user.id)" @keydown.enter = "SendMessage"  v-model="message" name="message-to-send" id="message-to-send" placeholder ="Type your message" rows="3"></textarea>
				
				

                <i class="fa fa-file-o"></i> &nbsp;&nbsp;&nbsp;
                <i class="fa fa-file-image-o"></i>

                <button>Send</button>

            </div> <!-- end chat-message -->

        </div> <!-- end chat -->

    </div> <!-- end container -->
</template>

<script>
  import _ from 'lodash'
    export default {
        name: "ChatApp",

       mounted(){
			   Echo.private(`chat.${authuser.id}`)
			   .listen('MessageSend',(e)=>{
			   this.selectUser(e.message.from);
			   //console.log(e.message.message);
			   });
				  this.$store.dispatch('userList');
				  
				  
				  
				  Echo.private('typingevent')
					.listenForWhisper('typing', (e) => {
				
					 if(e.user.id == this.userMessage.user.id && e.userId == authuser.id ){
					   this.typing = e.user.name;
					 }
						
						setTimeout(()=>{
						this.typing = '';
						
						},2000);
					});
				  
				   Echo.join('liveuser')
							.here((users) => {
								this.users = users
							})
							.joining((user) => {
								this.online = user ;
							})
							.leaving((user) => {
								console.log(user.name);
							});
				  
				  
				   },
				   
       data(){
       return{
	   message:'',
	   typing:'',
	   usera:[],
	   online:''

   }
       },
        computed:{
           userList(){
          return   this.$store.getters.userList
      } ,
	  userMessage(){
          return   this.$store.getters.userMessage
      }
   },

       created(){
	           
						 
        },
      methods:{
          selectUser(userId){
              this.$store.dispatch('userMessage',userId);
    },
		SendMessage(e){
			e.preventDefault();
			if(this.message!=''){
			axios.post('/sendmessage',{message:this.message, user_id:this.userMessage.user.id})
			.then(response=>{
				  this.selectUser(this.userMessage.user.id);
			})
			this.message='';
		  }
		},
		
		deleteSingleMessage(messageId){
		  axios.get(`/single_message_delete/${messageId}`)
		  .then(response=>{
		  this.selectUser(this.userMessage.user.id)
		  })
		},
		deleteAllMessage(){
		axios.get(`/delete_all_message/${this.userMessage.user.id}`)
		  .then(response=>{
		  this.selectUser(this.userMessage.user.id)
		  })
		},
		typingEvent(userId){
				Echo.private('typingevent')
			   .whisper('typing', {
				   'user': authuser,
				   'typing':this.message,
				   'userId':userId
			    });
		},
		onlineUser(userId){
		 return _.find(this.users,{'id':userId});
		}
    }
    }
</script>

<style scoped>

</style>
