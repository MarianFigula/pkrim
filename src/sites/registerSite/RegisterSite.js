import React, {useState} from "react";
import {Link, useNavigate} from "react-router-dom";
import "../../components/form/form.css"
import "../../spacing.css"
import {FormInput} from "../../components/formInput/FormInput";
import {Form} from "../../components/form/Form";
import axios from "axios";
import {useAuth} from "../../components/auth/AuthContext";

export function RegisterSite() {
    const [email, setEmail] = useState("")
    const [username, setUsername] = useState("")
    const [password, setPassword] = useState("")
    const [repeatedPassword, setRepeatedPassword] = useState("")
    const [securityQuestions] = useState([
        "-- Choose --",
        "What is your pet's name?",
        "What was your first car?",
        "What is your grandmother's name?",
        "What was the name of your first school?"
    ]);
    const [selectedSecurityQuestion, setSelectedSecurityQuestion] = useState("")

    const [securityAnswer, setSecurityAnswer] = useState("")
    const [error, setError] = useState("")
    const navigate = useNavigate()
    const { login } = useAuth(); // Access the login function from AuthContext

    const serverUrl = process.env.REACT_APP_SERVER_URL;

    async function handleSubmit(event) {
        event.preventDefault();

        if (password !== repeatedPassword) {
            setError("Password and Repeated password are not the same");
            return;
        }

        try {
            const response = await axios.post(`${serverUrl}/api/user/register.php`, {
                email,
                username,
                password,
                repeated_password: repeatedPassword,
                security_question: selectedSecurityQuestion,
                security_answer: securityAnswer
            }, {
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            const data = response.data;
            data.success ? login(data.token) : setError(data.message);

        } catch (error) {
            setError("Some inputs are not filled");
        }
    }

    return (
        <div className="login-container">
            <h2>Sign up</h2>
            <Form onSubmit={handleSubmit} error={error} submitLabel="Sign Up" buttonClassName="button-dark">
                <FormInput
                    label="Username"
                    type="text"
                    value={username}
                    onChange={(e) => setUsername(e.target.value)}
                    required
                />
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
                <FormInput
                    label="Repeat password"
                    type="password"
                    value={repeatedPassword}
                    onChange={(e) => setRepeatedPassword(e.target.value)}
                    required
                />
                <FormInput
                    label="Security question"
                    type="select"
                    value={selectedSecurityQuestion}
                    onChange={(e) => setSelectedSecurityQuestion(e.target.value)}
                    options={securityQuestions}
                />
                <FormInput
                    label="Security Answer"
                    type="text"
                    value={securityAnswer}
                    onChange={(e) => setSecurityAnswer(e.target.value)}
                    required
                />
            </Form>
            <div className="links">
                <Link to={"/login"}>Already registered?</Link>
            </div>

        </div>

    )
}