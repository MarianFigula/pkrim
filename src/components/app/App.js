import './App.css';
import {Route, Routes} from "react-router-dom";
import {LoginSite} from "../../sites/loginSite/LoginSite";
import {MainSite} from "../../sites/mainSite/MainSite";
import {RegisterSite} from "../../sites/registerSite/RegisterSite";
import {Header} from "../header/Header";
import {AdminSite} from "../../sites/adminSite/AdminSite";
import {UserProfileSite} from "../../sites/userProfileSite/UserProfileSite";
import {Footer} from "../footer/Footer";
import {AdminEditUserSite} from "../../sites/adminEditUserSite/AdminEditUserSite";
import {UserArtsSite} from "../../sites/userArtsSite/UserArtsSite";
import {UserReviewsSite} from "../../sites/userReviewsSite/UserReviewsSite";
import {ForgotPasswordSite} from "../../sites/forgotPasswordSite/ForgotPasswordSite";
import 'bootstrap-icons/font/bootstrap-icons.css';
import {CreateArtSite} from "../../sites/createArtSite/CreateArtSite";
import CartSite from "../../sites/cartSite/CartSite";
import PaymentSite from "../../sites/paymentSite/PaymentSite";
import {PaymentAccepted} from "../../sites/paymentAcceptedSite/PaymentAccepted";
import {PaymentDenied} from "../../sites/paymentDeniedSite/PaymentDenied";
import ProtectedRoute from "../auth/ProtectedRoute";
import {AuthProvider} from "../auth/AuthContext";


function App() {
    return (
        <AuthProvider>
            <Header />
            <Routes>
                {/* Public Routes */}
                <Route path="/login" element={<LoginSite />} />
                <Route path="/register" element={<RegisterSite />} />
                <Route path="/forgot-password" element={<ForgotPasswordSite />} />

                {/* Private Routes (ProtectedRoute handles access control) */}
                <Route path="/" element={<ProtectedRoute element={<MainSite />} />} />
                <Route path="/user-profile" element={<ProtectedRoute element={<UserProfileSite />} />} />
                <Route path="/admin" element={<ProtectedRoute element={<AdminSite />} />} />
                <Route path="/admin-edit-user/:id" element={<ProtectedRoute element={<AdminEditUserSite />} />} />
                <Route path="/my-arts" element={<ProtectedRoute element={<UserArtsSite />} />} />
                <Route path="/review-history" element={<ProtectedRoute element={<UserReviewsSite />} />} />
                <Route path="/upload-art" element={<ProtectedRoute element={<CreateArtSite />} />} />
                <Route path="/cart" element={<ProtectedRoute element={<CartSite />} />} />
                <Route path="/payment" element={<ProtectedRoute element={<PaymentSite />} />} />
                <Route path="/payment-accepted" element={<ProtectedRoute element={<PaymentAccepted />} />} />
                <Route path="/payment-denied" element={<ProtectedRoute element={<PaymentDenied />} />} />
            </Routes>
            <Footer />
        </AuthProvider>
    );
}

export default App;
