import { render, fireEvent } from '@testing-library/react';
import SearchBar from './SearchBar';
import '@testing-library/jest-dom';
import { AuthProvider } from '../auth/AuthContext';
import { BrowserRouter } from 'react-router-dom';

test('test to check if searchBar renders with correct default placeholder', () => {
    const { getByPlaceholderText } = render(
        <BrowserRouter>
            <AuthProvider>
                <SearchBar searchId="search" handleFilter={jest.fn()} />
            </AuthProvider>
        </BrowserRouter>
    );
    const input = getByPlaceholderText(/search/i);
    expect(input).toBeInTheDocument();
});


test('test that checks if searchBar renders  with custom placeholder', () => {
    const { getByPlaceholderText } = render(<SearchBar searchId="search" handleFilter={jest.fn()} placeholder="test placeholder" />);
    const input = getByPlaceholderText(/test placeholder/i);
    expect(input).toBeInTheDocument();
});

test('test to call handleFilter on input change', () => {
    const handleFilter = jest.fn();
    const { getByRole } = render(<SearchBar searchId="search" handleFilter={handleFilter} />);

    const input = getByRole('searchbox');

    fireEvent.change(input, { target: { value: 'test' } });

    expect(handleFilter).toHaveBeenCalledTimes(1);
    expect(handleFilter).toHaveBeenCalledWith(expect.any(Object));
});

