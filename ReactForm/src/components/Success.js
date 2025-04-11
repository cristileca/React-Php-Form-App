import React from 'react';

const Success = ({display}) => {
    return (
        <div className='success' style={{ textAlign: 'center', marginTop: '50px' , borderRadius:"25px", display:display}}>
            <h1>Success!</h1>
            <p>The form was submitted successfully.</p>
            <a href='/' className='btn-succes'>OK</a>
        </div>
    );
};

export default Success;