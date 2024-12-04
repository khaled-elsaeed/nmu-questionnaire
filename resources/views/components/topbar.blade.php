<!-- Start Topbar -->
<div class="topbar">
   <!-- Start row -->
   <div class="row align-items-center">
      <!-- Start col -->
      <div class="col-md-12 align-self-center">
         <div class="togglebar">
            <ul class="list-inline mb-0">
               <li class="list-inline-item">
                  <div class="menubar">
                     <a class="menu-hamburger" href="javascript:void();">
                     <img src="{{ asset('images/svg-icon/close.svg') }}" class="img-fluid menu-hamburger-close" alt="close">
                     <img src="{{ asset('images/svg-icon/collapse.svg') }}" class="img-fluid menu-hamburger-collapse" alt="collapse">
                     </a>
                  </div>
               </li>
            </ul>
         </div>
         <div class="infobar">
            <ul class="list-inline mb-0">
            
               <li class="list-inline-item">
                  <div class="notifybar">
                     <div class="dropdown">
                        <a class="dropdown-toggle infobar-icon" href="#" role="button" id="notoficationlink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="{{ asset('images/svg-icon/notifications.svg') }}" class="img-fluid" alt="notifications">
                        <span class="live-icon"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notoficationlink">
                           <div class="notification-dropdown-title">
                              <h4>
                                 <i class="feather icon-bell"></i>
                                 Notifications
                              </h4>
                           </div>
                           <ul class="list-unstyled">
                              @if(auth()->user()->notifications->isEmpty())

                              <li class="d-flex p-2 mt-1 dropdown-item">
                                 <div class="media-body">
                                    <p>No new notifications</p>
                                 </div>
                              </li>
                              @else
                              @foreach(auth()->user()->notifications as $notification)
                              <li class="d-flex p-2 mt-1 dropdown-item">
                              <span class="action-icon badge badge-info">
    <i class="feather icon-check-circle"></i>
</span>

                                 <div class="media-body">

                                    <h5 class="action-title">{{ $notification->data['title'] }}</h5>
                                    <p>{{ $notification->data['message'] }}</p>
                                    <p><span class="timing">{{ \Carbon\Carbon::parse($notification->created_at)->setTimezone('Africa/Cairo')->diffForHumans() }}</span></p>
                                 </div>
                              </li>
                              
                              @endforeach
                              @endif
                           </ul>
                        </div>
                     </div>
                  </div>
               </li>
               <li class="list-inline-item">
                  <div class="profilebar">
                     <div class="dropdown">
                        <a class="dropdown-toggle" href="#" role="button" id="profilelink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="{{ asset('images/users/profile.svg') }}" class="img-fluid" alt="profile"><span class="feather icon-chevron-down live-icon"></span></a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profilelink">
                           <div class="dropdown-item">
                              <div class="profilename">
                                 <h5>{{ auth()->user()->username_en}}</h5>
                              </div>
                           </div>
                           <div class="userbox">
                              <ul class="list-unstyled mb-0">
                                 <li class="d-flex p-2 mt-1 dropdown-item">
                                    <a href="#" class="profile-icon"><img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="user">My Profile</a>
                                 </li>
                                 <li class="d-flex p-2 mt-1 dropdown-item">
                                    <a href="#" class="profile-icon"><img src="{{ asset('images/svg-icon/email.svg') }}" class="img-fluid" alt="email">Email</a>
                                 </li>
                                 <li class="d-flex p-2 mt-1 dropdown-item">
                                    <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                       @csrf
                                       <button type="submit" class="profile-icon" style="border: none; background: none; cursor: pointer;">
                                       <img src="{{ asset('images/svg-icon/logout.svg') }}" class="img-fluid" alt="logout">Logout
                                       </button>
                                    </form>
                                 </li>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </div>
               </li>
            </ul>
         </div>
      </div>
      <!-- End col -->
   </div>
   <!-- End row -->
</div>
<!-- End Topbar -->