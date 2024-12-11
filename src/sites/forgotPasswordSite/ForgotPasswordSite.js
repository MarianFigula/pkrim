import {Form} from "../../components/form/Form";
import React, {useState} from "react";
import {FormInput} from "../../components/formInput/FormInput";
import {Link} from "react-router-dom";
import axios from "axios";
import {useAuth} from "../../components/auth/AuthContext";

export function ForgotPasswordSite() {

    const [error, setError] = useState("")
    const [email, setEmail] = useState("")
    const [password, setPassword] = useState("")
    const [repeatedPassword, setRepeatedPassword] = useState("")
    const [securityAnswer, setSecurityAnswer] = useState("")
    const serverUrl = process.env.REACT_APP_SERVER_URL;
    const {login} = useAuth()

    const handleSubmit = async (event) => {
        event.preventDefault();

        if (password !== repeatedPassword) {
            setError("Password and Repeated password are not the same");
            return;
        }

        try {
            const response = await axios.post(`${serverUrl}/api/user/forgotPassword.php`, {
                email,
                password,
                repeated_password: repeatedPassword,
                security_answer: securityAnswer
            }, {
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            const data = response.data;

            if (data.success){
                console.log(data);
                login(data.token)
            }else {
                setError(data.message);
            }

        } catch (error) {
            setError("Something failed. Please try again");
        }
    };
    return (
        <>
            <div className="login-container">
                <h2>Renew password</h2>
                <Form
                    error={error}
                    submitLabel="Change password"
                    buttonClassName="button-dark"
                    onSubmit={handleSubmit}
                >
                    <FormInput
                        label="Email"
                        type="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                    />
                    <FormInput
                        label="Security Answer"
                        type="text"
                        value={securityAnswer}
                        onChange={(e) => setSecurityAnswer(e.target.value)}
                        required
                    />
                    <FormInput
                        label="Password"
                        type="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        required
                    />
                    <FormInput
                        label="Repeat password"
                        type="password"
                        value={repeatedPassword}
                        onChange={(e) =>
                            setRepeatedPassword(e.target.value)}
                        required
                    />
                </Form>
                <div className="links">
                    <Link to={"/login"}>Sign in</Link>
                    <Link to={"/register"}>Sign up</Link>
                </div>
            </div>
        </>
    )
}