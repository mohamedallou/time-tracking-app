import {isAuthenticated} from "@/security/authentication.js";

export const authGuard = async (to, from) => {
    // reject the navigation
    return await isAuthenticated();
}