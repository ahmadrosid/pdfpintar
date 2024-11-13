import { initializeApp } from "firebase/app";
import { getAuth, GoogleAuthProvider, getRedirectResult, signInWithPopup } from "firebase/auth";

async function initAuth() {
    if (['/login', '/register'].includes(window.location.pathname)) {
        console.log('Auth with firebase started!')
        const firebaseConfig = {
            apiKey: import.meta.env.VITE_APP_FIREBASE_API_KEY,
            authDomain: "pdfpintar-app.firebaseapp.com",
            projectId: "pdfpintar-app",
            storageBucket: "pdfpintar-app.firebasestorage.app",
            messagingSenderId: "804053762443",
            appId: import.meta.env.VITE_APP_FIREBASE_APP_ID
        };
        
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);

        window.loginWithGoogle = async () => {
            await signInWithPopup(auth, new GoogleAuthProvider())
                .then(function (result) {
                    result.user.getIdToken().then(function(result) {
                        document.getElementById('social-login-tokenId').value = result;
                        document.getElementById('social-login-form').submit();
                    });
                });
        }
    }
}

initAuth();
