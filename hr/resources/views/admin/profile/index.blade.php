@extends('admin.layout.link')

@section('content')
    <section class="section profile">
        <div class="row">
            <div class="col-xl-4">

                <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

                        <img src="{{ auth()->user() && auth()->user()->image ? asset('storage/' . auth()->user()->image) : asset('admin/img/profile-img.jpg') }}"
                            alt="Profile" class="rounded-circle">
                        <h2>{{ auth()->user()->username ?? 'null' }}</h2>
                        <div class="social-links mt-2">
                            <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-xl-8">

                <div class="card">
                    <div class="card-body pt-3">
                        <!-- Bordered Tabs -->
                        <ul class="nav nav-tabs nav-tabs-bordered" role="tablist">

                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview"
                                    aria-selected="true" role="tab">Overview</button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit"
                                    aria-selected="false" tabindex="-1" role="tab">Edit Profile</button>
                            </li>

                            <!-- <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings"
                                    aria-selected="false" tabindex="-1" role="tab">Settings</button>
                            </li> -->

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password"
                                    aria-selected="false" tabindex="-1" role="tab">Change Password</button>
                            </li>

                        </ul>
                        <div class="tab-content pt-2">

                            <div class="tab-pane fade show active profile-overview" id="profile-overview" role="tabpanel">
                                <h5 class="card-title">About</h5>
                                <p class="small fst-italic">Sunt est soluta temporibus accusantium neque nam maiores cumque
                                    temporibus. Tempora libero non est unde veniam est qui dolor. Ut sunt iure rerum quae
                                    quisquam autem eveniet perspiciatis odit. Fuga sequi sed ea saepe at unde.</p>

                                <h5 class="card-title">Profile Details</h5>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label ">Username </div>
                                    <div class="col-lg-9 col-md-8">{{ auth()->user()->username  ?? 'null' }}</div>
                                </div>                           



                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Phone</div>
                                    <div class="col-lg-9 col-md-8">{{ auth()->user()->mobile ?? 'null' }}</div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Email</div>
                                    <div class="col-lg-9 col-md-8">{{ auth()->user()->email ?? 'null' }}</div>
                                </div>

                            </div>

                            <div class="tab-pane fade profile-edit pt-3" id="profile-edit" role="tabpanel">

                                <!-- Profile Edit Form -->
                                <form action="{{ route('admin.profile.update') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" value="{{ auth()->user()->id ?? 'null' }}">
                                
                                    <!-- Profile Image Display -->
                                    <div class="row mb-3">
                                        <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                                        <div class="col-md-8 col-lg-9">
                                            <img src="{{ auth()->user() && auth()->user()->image ? asset('storage/' . auth()->user()->image) : asset('admin/img/profile-img.jpg') }}" alt="Profile">
                                        </div>
                                    </div>
                                
                                    <!-- New Profile Image Input -->
                                    <div class="row mb-3">
                                        <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile New Image</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input type="file" name="image" class="form-control">
                                        </div>
                                    </div>
                                
                                    <!-- Username Field -->
                                    <div class="row mb-3">
                                        <label for="username" class="col-md-4 col-lg-3 col-form-label">Username</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="username" type="text" class="form-control" value="{{ auth()->user()->username }}">
                                        </div>
                                    </div>
                                
                                    <!-- Mobile Field -->
                                    <div class="row mb-3">
                                        <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="mobile" type="text" class="form-control" value="{{ auth()->user()->mobile }}">
                                        </div>
                                    </div>
                                
                                    <!-- Email Field -->
                                    <div class="row mb-3">
                                        <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="email" type="email" class="form-control" value="{{ auth()->user()->email }}">
                                        </div>
                                    </div>
                                
                                    <!-- Submit Button -->
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                                
                                
                            </div>
                            <div class="tab-pane fade pt-3" id="profile-settings" role="tabpanel">

                                <!-- Settings Form -->
                                <form>

                                    <div class="row mb-3">
                                        <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Email
                                            Notifications</label>
                                        <div class="col-md-8 col-lg-9">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="changesMade"
                                                    checked="">
                                                <label class="form-check-label" for="changesMade">
                                                    Changes made to your account
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="newProducts"
                                                    checked="">
                                                <label class="form-check-label" for="newProducts">
                                                    Information on new products and services
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="proOffers">
                                                <label class="form-check-label" for="proOffers">
                                                    Marketing and promo offers
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="securityNotify"
                                                    checked="" disabled="">
                                                <label class="form-check-label" for="securityNotify">
                                                    Security alerts
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form><!-- End settings Form -->

                            </div>

                            <div class="tab-pane fade pt-3" id="profile-change-password" role="tabpanel">
                                <!-- Change Password Form -->
                                <form action="{{ route('admin.password.update') }}" method="post">
                                  @csrf
                                  @method('PUT')
                                  
                                  <!-- Current Password -->
                                  <div class="row mb-3">
                                      <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                                      <div class="col-md-8 col-lg-9">
                                          <input name="current_password" type="password" class="form-control" id="currentPassword" required>
                                      </div>
                                  </div>
                                  
                                  <!-- New Password -->
                                  <div class="row mb-3">
                                      <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                                      <div class="col-md-8 col-lg-9">
                                          <input name="password" type="password" class="form-control" id="newPassword" required>
                                      </div>
                                  </div>
                                  
                                  <!-- Re-enter New Password (confirm password) -->
                                  <div class="row mb-3">
                                      <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                                      <div class="col-md-8 col-lg-9">
                                          <input name="password_confirmation" type="password" class="form-control" id="renewPassword" required>
                                      </div>
                                  </div>
                                  
                                  <div class="text-center">
                                      <button type="submit" class="btn btn-primary">Change Password</button>
                                  </div>
                              </form>
                              
                              
                              <!-- End Change Password Form -->

                            </div>

                        </div><!-- End Bordered Tabs -->

                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
