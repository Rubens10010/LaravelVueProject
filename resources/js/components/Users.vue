<template>
    <div class="container">
	  <div class="row mt-5">
		<div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Users List</h3>

                <div class="card-tools">
					<button class="btn btn-success" @click="newModal"> Add new <i class="fas fa-user-plus fa-fw"></i>
					</button>
				</div>
				
			  </div>
			  
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Email</th>
					  <th>Type</th>
					  <th>Registered At</th>
                      <th>Modify</th>
                    </tr>
                  </thead>
                  <tbody>
					<tr v-for="user in users" :key="user.id">
                      <td>{{user.id}}</td>
                      <td>{{user.name}}</td>
					  <td>{{user.email}}</td>
					  <td>{{user.type | upText}}</td>
					  <td>{{user.created_at | myDate}}</td>
                      <td>
						<a href="#" @click="editModal(user)">
							<i class="fa fa-edit green"></i>
						</a>
						/
						<a href="#" @click="deleteUser(user.id)">
							<i class="fa fa-trash red"></i>
						</a>
					  </td>
                    </tr>
                    
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
		</div>
	
	
	<!-- Modal -->
	<div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
				<h5 class="modal-title" id="addNewLabel" v-show="!editmode">Add New User</h5>
				<h5 class="modal-title" id="addNewLabel" v-show="editmode">Update User Info</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				</div>

				<form @submit.prevent="editmode ? updateUser : createUser">
				<div class="modal-body">

					<div class="form-group">
						<input v-model="form.name" type="text" name="name" placeholder="Name" 
						  class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
						<has-error :form="form" field="name"></has-error>
					</div>

					<div class="form-group">
						<input v-model="form.email" type="email" name="email" placeholder="Email Address" 
						  class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
						<has-error :form="form" field="email"></has-error>
					</div>

					<div class="form-group">
						<textarea v-model="form.bio" name="bio" id="bio" placeholder="Short bio for user (Optional)" 
						  class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
						<has-error :form="form" field="bio"></has-error>
					</div>

					<div class="form-group">
						<select name="type" v-model="form.type" id="type" class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
							<option value="">Select User Role</option>
							<option value="admin">Admin</option>
							<option value="user">Standard User</option>
							<option value="author">Author</option>
						</select>
						<has-error :form="form" field="type"></has-error>
					</div>

					<div class="form-group">
						<input v-model="form.password" type="password" name="password" id="password" placeholder="Password" 
						  class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
						<has-error :form="form" field="password"></has-error>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					<!-- If editmode=true -->
					<button v-show="editmode" type="submit" class="btn btn-success">Update</button>
					<button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
				</div>

				</form>
			</div>
		</div>
	</div>

	</div>
</template>

<script>
    export default {
		data(){
			return {
				editmode: false,
				form: new Form({
					id: '',
					name: '',
					email: '',
					password: '',
					type: '',
					bio: '',
					photo: ''
				}),
				users: {}
			}
		},
		methods: {
			createUser(){
				this.$Progress.start();
				this.form.post('api/user').then(() => {
					Fire.$emit('UsersChanged');	// event
					// Toaster sweet view
					Toast.fire({
						icon: 'success',
						title: 'User created successfully'
					});
					$('#addNew').modal('hide');
					$('.modal-backdrop').hide();
				}).catch(()=>{

				})

				this.$Progress.finish();
			},
			loadUsers(){
				axios.get("api/user").then(({data}) => (this.users = data.data));
			},
			updateUser(){
				this.$Progress.start();
				// PUT|PATCH REQUEST
				/*this.form.put('api/user/'+this.form.id).then(()=>{
					// success
					this.$Progress.finish();
				}).catch(()=>{
					//this.$Progress.fail();
				});*/
			},
			deleteUser(id){
				// Ajax request to server
				Swal.fire({
					title: 'Are you sure?',
					text: "You won't be able to revert this!",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes, delete it!'
					}).then((result) => {
						if(result.value){
							// Send AJAX request
							// using vform -> from routes
							this.form.delete('api/user/'+id).then(()=>{
								Swal.fire(
								'Deleted!',
								'This User has been deleted.',
								'success'
								)
								Fire.$emit('UsersChanged');	// event
							}).catch(()=>{
							Swal.fire("Failed!", )
							});
						}
					});
			},
			newModal(){
				this.editmode = false;
				// Reset form with vform
				this.form.reset();
				$('#addNew').modal('show');
			},
			editModal(user){
				this.editmode = true;
				// Reset form with vform
				this.form.reset();
				$('#addNew').modal('show');
				// Populate User data
				this.form.fill(user);
			}
		},
        mounted() {
			this.loadUsers();
			// Listening for event
			Fire.$on('UsersChanged',() => {
				this.loadUsers();
			});
			// Fire every 3 seconds
			//setInterval(() => this.loadUsers(), 3000);
        }
    }
</script>