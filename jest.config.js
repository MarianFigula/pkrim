module.exports = {
    transform: {
        '^.+\\.jsx?$': 'babel-jest', // Transform .js and .jsx files using babel-jest
    },
    testEnvironment: 'jsdom', // Use jsdom environment for DOM-related tests
};