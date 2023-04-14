import React from 'react'

export const LoginMail = ({ id }) => {
    return (
        <input
            id={id}
            type="mail"
            placeholder="Email"
            name="email"
            className="dash__input"
            autoComplete="new-password"
        />
    )
}
