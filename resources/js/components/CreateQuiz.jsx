import React, {useEffect, useState} from 'react';
import ReactDOM from 'react-dom';
import RawHTML from './RawHTML';

export const CreateQuiz = (props) => {
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [duration, setDuration] = useState(60);
    const [valid, setValid] = useState(false);
    const [error, setError] = useState('');
    const [showError, setShowError] = useState(false);

    const validate = () => {
        if (error.length > 0) {
            setShowError(true);
            setTimeout(() => {
                setShowError(false);
            }, 5000);
        }
    };

    useEffect(() => {
        let valid = true;
        if (title.length < 10) {
            setError('Title should be at least 10 characters long.');
            valid = false;
        } else if (description.length < 20) {
            setError('Description should be at least 20 characters long.');
            valid = false;
        } else if (!Number.isInteger(duration) || (duration < 5 && duration > 180)) {
            setError('Duration must be between 5 to 180 minutes.');
            valid = false;
        }
        setValid(valid);
    }, [title, description, duration]);

    return (
        <div className="col">
            <div className="card">
                <div className="card-header">
                    <h3>Create a new Quiz</h3>
                </div>

                <div className="card-body">
                    <div className="row">
                        {
                            showError
                                ? <div className="col-12"><div className="alert alert-danger" role="alert">{error}</div></div>
                                : <RawHTML className="col-12">{props.children}</RawHTML>
                        }

                        <div className="form-group col-12">
                            <label>Quiz Title</label>
                            <input className="form-control" type="text" name="title"
                                   placeholder="Basics of C++ programming language"
                                   aria-describedby="titleHelp"
                                   onChange={(event) => setTitle(event.target.value)}
                                   value={title}
                            />
                            <small id="titleHelp" className="form-text text-muted">Provide a short descriptive quiz title.</small>
                        </div>

                        <div className="form-group col-12">
                            <label>Description</label>
                            <textarea className="form-control" name="description" rows="3"
                                      placeholder="Basics of C++ programming language"
                                      aria-describedby="questionHelp"
                                      onChange={(event) => setDescription(event.target.value)}
                                      defaultValue={description}
                            />
                            <small id="questionHelp" className="form-text text-muted">Provide a short description and rules for the quiz.</small>
                        </div>

                        <div className="form-group col-6">
                            <div className="input-group">
                                <div className="input-group-prepend">
                                    <span className="input-group-text">Test Duration</span>
                                </div>
                                <input type="number" className="form-control" name="duration" min="5" max="180"
                                       placeholder="60" aria-describedby="quiz-duration-hint"
                                       onChange={(event) => setDuration(event.target.value)}
                                       value={duration}
                                />
                                <div className="input-group-append">
                                    <span className="input-group-text">minutes</span>
                                </div>
                            </div>
                            <small id="titleHelp" className="form-text text-muted">Duration for someone to complete the test.</small>
                        </div>
                    </div>

                    <div className="form-group col-12">
                        {
                            valid
                                ? <button type="submit" className="btn btn-success mr-2 float-right">Create Quiz</button>
                                : <button type="button" onClick={validate} className="btn btn-outline-success mr-2 float-right">Create Quiz</button>
                        }
                    </div>
                </div>
            </div>
        </div>
    );
};

if (document.getElementById('CreateQuiz')) {
    ReactDOM.render(<CreateQuiz>{document.querySelector('#CreateQuiz').innerHTML}</CreateQuiz>,
        document.querySelector('#CreateQuiz'));
}
