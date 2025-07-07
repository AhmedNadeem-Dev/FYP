// resources/js/router.js
import { createRouter, createWebHistory } from 'vue-router';

// Importing all components
import UserProfile from './components/UserProfile.vue'; // Import the user profile component
import FAQ from './components/FAQ.vue';
import Home from './components/Home.vue';
import ProductDetails from './components/ProductDetails.vue';
import ArtistDetail from './components/ArtistDetail.vue';
import Login from './components/Login.vue';
import Signup from './components/Signup.vue';
import CustomRequest from './components/CustomRequest.vue';
import AddProduct from './components/AddProduct.vue';
// import Account from './components/Account.vue';
import PasswordChange from './components/PasswordChange.vue';
import ManageProducts from './components/ManageProducts.vue';
import ForgetPassword from './components/ForgetPassword.vue'; // Import ForgetPassword component
import About from './components/About.vue';
import VerifyCode from '@/components/VerifyCode.vue';
import ResetPassword from '@/components/ResetPassword.vue';
import ViewCustomRequests from './components/ViewCustomRequests.vue';
import ArtistViewCustomRequests from './components/ArtistViewCustomRequests.vue';
import ViewAcceptedRequests from './components/ViewAcceptedRequests.vue';
import RequestDetailPage from './components/RequestDetailPage.vue';  
import BrowseScrap from './components/BrowseScrap.vue';
import AdminDashboard from '@/components/AdminDashboard.vue';
import ArtistDashboard from '@/components/ArtistDashboard.vue';
import ScrapSellerDashboard from '@/components/ScrapSellerDashboard.vue';
import MyInvitations from './components/MyInvitations.vue';
import ProjectCreate from './components/ProjectCreate.vue';
import ProjectDetail from './components/ProjectDetail.vue';
import ProjectList from './components/ProjectList.vue';
import MySentInvitations from './components/MySentInvitations.vue';
import AdminFeedback from './components/AdminFeedback.vue';
import ChangeEmail from './components/ChangeEmail.vue';     
import Wishlist from './components/Wishlist.vue';
import AddCart from './components/AddCart.vue'; // Import the cart component
import Portfolio from './components/Portfolio.vue';
import CompletedProjects from './components/CompletedProjects.vue';
import OrderHistory from './components/OrderHistory.vue';
import Checkout from './components/Checkout.vue';
import OrdersReceived from './components/OrdersReceived.vue';
import Recommendations from './components/BrowseScrap.vue'
const routes = [  {
  path: '/user-profile/:id',
  name: 'UserProfile',
  component: UserProfile,
}, 
  {
    path: '/manage-products',
    name: 'ManageProducts',
    component: ManageProducts,
    meta: { requiresAuth: true }
  },
{
  path: '/my-sent-invitations',
  name: 'MySentInvitations',
  component: MySentInvitations
},
{
  path:'/FAQ',
  name: 'FAQ',
  component: FAQ
},
{
  path: '/projects/completed',
  name: 'CompletedProjects',
  component: CompletedProjects,
  meta: {
    title: 'Completed Projects',
    requiresAuth: false // This page is publicly accessible
  }
},

{
  path: '/cart',
  name: 'cart',
  component: AddCart,
  meta: { requiresAuth: true } // Since your cart requires authentication
},
{
  path: '/products/:id', // Path for product details with dynamic ID
  name: 'product-details',
  component: ProductDetails,
  props: true, // Pass the `id` as a prop to the ProductDetails component
},
{
  path: '/about',
  name:'about',
  component: About,
},
{
  path: '/scrap-items',   // Route for browsing all scrap products
  name: 'browse-scrap',
  component: BrowseScrap,
},
  { path: '/', component: Home },
  // { path: '/item/:itemId', component: ItemDetail, props: true },
  { path: '/artist/:artistId', component: ArtistDetail, props: true },
  { path: '/login', component: Login },
  { path: '/add-product', component: AddProduct },
  { path: '/signup', component: Signup },
  { path: '/admin-dashboard', name: 'AdminDashboard', component: AdminDashboard },
  { path: '/artist-dashboard', name: 'ArtistDashboard', component: ArtistDashboard },
  { path: '/scrap-seller-dashboard', name: 'ScrapSellerDashboard', component: ScrapSellerDashboard },
  { path: '/custom-request', component: CustomRequest },
  { path: '/admin-feedback', component: AdminFeedback },
  { path: '/portfolio', component: Portfolio },
  {
    path: '/email-change',
    name: 'ChangeEmail',
    component: ChangeEmail,
  },
  {
    path: '/my-invitations',
    name: 'MyInvitations',
    component: MyInvitations
  },
  {
    path: '/wishlist',
    name: 'wishlist',
    component: Wishlist,
    meta: { requiresAuth: true } // Optional: Add auth protection
  },
  {
    path: '/projects/create',
    name: 'ProjectCreate',
    component: ProjectCreate
  },
  {
    // For viewing a specific project (details, collaboration, etc.)
    path: '/projects/:id',
    name: 'ProjectDetail',
    component: ProjectDetail,
    props: true  // allows route params -> props
  },
  {
    path: '/orders-received',
    name: 'OrdersReceived',
    component: OrdersReceived,
    meta: { requiresAuth: true, requiresSeller: true }
  },
  {
    path: '/checkout',
    name: 'checkout',
    component: Checkout,
    meta: { requiresAuth: true }
  },
  {
    path: '/recommendations',
    name: 'recommendations',
    component: BrowseScrap,
    meta: { requiresAuth: true }
  },
  {
    path: '/order-history',
    name: 'order-history',
    component: OrderHistory,
    meta: { requiresAuth: true }
  }, 
  {
    path: '/order-history/:orderId',
    name: 'order-history-with-id',
    component: OrderHistory,
    meta: { requiresAuth: true }
  },
  {
    path: '/projects',
    name: 'ProjectList',
    component: ProjectList
  },
  {
    path: '/verify-code',
    name: 'VerifyCode',
    component: VerifyCode,
  },
  {
    path: '/reset-password',
    name: 'ResetPassword',
    component: ResetPassword,
  },
  // {
  //   path: "/account",
  //   name: "UserProfile",
  //   component: UserProfile,
  // },
  {
    path: "/password-change",
    name: "PasswordChange",
    component: PasswordChange,
  },
  { path: '/forget-password', component: ForgetPassword }, // New route for Forget Password
//for general/scrap user
  { path: '/view-custom-requests', component: ViewCustomRequests, name: 'ViewCustomRequests' },
//to view all the requests
  {
    path: '/artist-view-custom-requests',
    component: ArtistViewCustomRequests,
    // name: 'ArtistViewCustomRequests',
    // meta: { requiresAuth: true, role: 'artist' },
  },
  //to view the requests that the artist has accepted
  {
    path: '/view-accepted-requests',
    name: 'ViewAcceptedRequests',
    component: ViewAcceptedRequests,
    meta: { requiresAuth: true }, // Only authenticated users can access this page
  }
,  
{
  path: '/custom-requests/:id', // Dynamic route to view a single request's details
  name: 'RequestDetailPage',
  component: RequestDetailPage,
  props: true,
},

  { path: '/:pathMatch(.*)*', redirect: '/' } // Redirect unknown routes to Home
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Route guards for authentication
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('access_token');
  const userSession = localStorage.getItem('userSession');
  
  // Check if route requires authentication
  if (to.meta.requiresAuth && !isAuthenticated) {
    // Redirect to login if not authenticated
    next('/login');
    return;
  }
  
  // Check if route requires specific role
  if (to.meta.requiresSeller && userSession) {
    try {
      const user = JSON.parse(userSession);
      if (user.role !== 'scrapSeller') {
        next('/'); // Redirect to home if not a seller
        return;
      }
    } catch (e) {
      next('/login');
      return;
    }
  }
  
  // Check if route requires artist role
  if (to.meta.requiresArtist && userSession) {
    try {
      const user = JSON.parse(userSession);
      if (user.role !== 'artist') {
        next('/'); // Redirect to home if not an artist
        return;
      }
    } catch (e) {
      next('/login');
      return;
    }
  }
  
  // Check if route requires admin role
  if (to.meta.requiresAdmin && userSession) {
    try {
      const user = JSON.parse(userSession);
      if (user.role !== 'admin') {
        next('/'); // Redirect to home if not admin
        return;
      }
    } catch (e) {
      next('/login');
      return;
    }
  }
  
  // Redirect authenticated users away from login/signup
  if ((to.path === '/login' || to.path === '/signup') && isAuthenticated) {
    next('/');
    return;
  }
  
  next();
});

export default router;