import React, {useEffect, useState} from "react";
import { Link, useNavigate } from "react-router-dom";
import "../../components/form/form.css";
import "../../spacing.css";
import { FormInput } from "../../components/formInput/FormInput";
import { Form } from "../../components/form/Form";
import axios from "axios";
import {useAuth} from "../../components/auth/AuthContext";
import {hashString} from "../../hashUtils";

export function LoginSite() {
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [error, setError] = useState("");
    const navigate = useNavigate();
    const serverUrl = process.env.REACT_APP_SERVER_URL;
    const { login, token } = useAuth();

    useEffect(() => {
        if (token) {
            navigate("/");
        }
    }, [token]);
    
    
    async function handleSubmit(event) {
        event.preventDefault();

        try {
            const response = await axios.post(
                `${serverUrl}/api/user/login.php`,
                {
                    email: email,
                    password: hashString(password),
                },
                {
                    headers: {
                        "Content-Type": "application/json",
                    },
                }
            );

            const data = response.data;
            data.success ? login(data.token) : setError(data.message);
        } catch (error) {
            setError("Error, try again");
        }
    }

    return (
        <div className="login-container">
            <h2>Sign in</h2>
            <Form
                onSubmit={handleSubmit}
                error={error}
                submitLabel="Sign In"
                buttonClassName="button-dark"
            >
                <FormInput
                    label="Email"
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    required
                />
                <FormInput
                    label="Password"
                    type="password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    required
                />
            </Form>

            <div className="links">
                <Link to="/register">Register</Link>
                <Link to="/forgot-password">Forgot password?</Link>
            </div>
        </div>
    );
}
