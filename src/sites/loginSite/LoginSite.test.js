import React from "react";
import { render, screen, fireEvent } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import '@testing-library/jest-dom'; // For additional matchers
import { LoginSite } from "./LoginSite"; // Replace with the actual path to your component
import { MemoryRouter } from "react-router-dom";
import { Form } from "../../components/form/Form";
import { FormInput } from "../../components/formInput/FormInput";
import { AuthProvider } from '../../components/auth/AuthContext';

describe("Login Component", () => {

    test("renders the login form with inputs and submit button", () => {
        render(
            <MemoryRouter>
                <AuthProvider>
                <LoginSite />
                    </AuthProvider>
            </MemoryRouter>
        );

        // Check that the heading renders
        expect(screen.getByText("Sign in")).toBeInTheDocument();

        // Check that email and password inputs render with correct labels
        const emailInput = screen.getByLabelText("Email");
        expect(emailInput).toBeInTheDocument();
        expect(emailInput).toHaveAttribute("type", "email");

        const passwordInput = screen.getByLabelText("Password");
        expect(passwordInput).toBeInTheDocument();
        expect(passwordInput).toHaveAttribute("type", "password");

        // Check that the submit button renders
        expect(screen.getByRole("button", { name: "Sign In" })).toBeInTheDocument();
    });


    test("allows the user to input email and password", async () => {
        render(
            <MemoryRouter>
                <AuthProvider>
                <LoginSite />
                    </AuthProvider>
            </MemoryRouter>
        );

        const emailInput = screen.getByLabelText("Email");
        const passwordInput = screen.getByLabelText("Password");

        await userEvent.type(emailInput, "user@example.com");
        await userEvent.type(passwordInput, "password123");

        expect(emailInput).toHaveValue("user@example.com");
        expect(passwordInput).toHaveValue("password123");
    });

    test("calls handleSubmit when the form is submitted", async () => {
        const handleSubmitMock = jest.fn(); // Mock function
        render(
            <Form
                onSubmit={handleSubmitMock}
                error={null}
                submitLabel="Sign In"
                buttonClassName="button-dark"
            >
                <FormInput
                    label="Email"
                    type="email"
                    value=""
                    onChange={() => {}}
                    required
                />
                <FormInput
                    label="Password"
                    type="password"
                    value=""
                    onChange={() => {}}
                    required
                />
            </Form>
        );

        const submitButton = screen.getByRole("button", { name: "Sign In" });
        await userEvent.click(submitButton);

        expect(handleSubmitMock).toHaveBeenCalledTimes(1);
    });

    test("displays an error message when there is an error", async () => {
        const handleSubmitMock = jest.fn().mockRejectedValueOnce(new Error("Login failed. Please try again"));

        render(
            <MemoryRouter>
                <Form
                    onSubmit={handleSubmitMock}
                    error="Login failed. Please try again"
                    submitLabel="Sign In"
                    buttonClassName="button-dark"
                >
                    <FormInput
                        label="Email"
                        type="email"
                        value=""
                        onChange={() => {}}
                        required
                    />
                    <FormInput
                        label="Password"
                        type="password"
                        value=""
                        onChange={() => {}}
                        required
                    />
                </Form>
            </MemoryRouter>
        );

        const submitButton = screen.getByRole("button", {name: "Sign In"});
        await userEvent.click(submitButton);

        const errorMessage = await screen.findByText(/Login failed. please try again/i);
        expect(errorMessage).toBeInTheDocument();
    });

});
