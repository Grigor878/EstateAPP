import React from 'react'
import '../../../../components/inputs/Inputs.scss'

export const SingleSelect = ({ title, id, value, onChange, data }) => {
    return (
        <label className='addproperties__card-singleselect'>
            {title}*
            <select id={id} value={value} onChange={onChange} className="addproperties__card-singleselect-dropdown">
                {data.map((el) => {
                    return (
                        <option
                            // disabled={el.id === 1 ? "disabled" : null}
                            key={el.id}
                            value={el.value}
                        >{el.value}
                        </option>
                    )
                })}
            </select>
        </label>
    )
}