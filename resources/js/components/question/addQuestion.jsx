import React, {useEffect, useState} from 'react';
import ReactDOM from 'react-dom';

export let AddQuestion;
AddQuestion = () => {
    const [question, setQuestion] = useState('');
    const [optionType, setOptionType] = useState('radio');
    const [optionCount, setOptionCount] = useState(4);
    const [options, setOptions] = useState([
        {title: '', media: ''}, {title: '', media: ''}, {title: '', media: ''}, {title: '', media: ''}
    ]);
    const [error, setError] = useState('');
    const [valid, setValid] = useState(false);

    const updateOptionCount = (number, index = null) => {
        event.preventDefault();
        if ((number < 0 && optionCount > 2) || (number > 0 && optionCount < 8)) {
            setOptionCount(optionCount + number);
            if (number < 0) {
                setOptions([...options.slice(0, index), ...options.slice(index + 1)]);
            } else {
                setOptions([...options, ...[{title: '', media: ''}]])
            }
        }
    };

    const updateOption = (event, index) => {
        let value = event.target.value;
        setOptions([...options.slice(0, index), {title: value, media: ''}, ...options.slice(index + 1)]);
    };

    useEffect(() => {
        let valid = true;
        if (error.length > 0 || question.legth < 10 ||
            (optionType !== 'radio' && optionType !== 'checkbox') ||
            (optionCount < 2 && optionCount > 8)) {
            valid = false;
        }
        // mark validity invalid if any options is blank
        if (options.filter((i) => !i.title.length).length) {
            valid = false;
        }
        setValid(valid);
    }, [question, options, error]);

    return (
        <div className="col">
            <div className="card">
                <div className="card-header">
                    <h3>Add Question</h3>
                </div>

                <div className="card-body">
                    <div className="row">
                        {
                            error.length > 0 &&
                            <div className="alert alert-danger col-12" role="alert">
                                {error}
                            </div>
                        }
                        <div className="form-group col-12">
                            <label>Question</label>
                            <textarea className="form-control" name="question" rows="3" required
                                      placeholder="C++ is an _____ programming language?"
                                      aria-describedby="questionHelp"
                                      onChange={(event) => setQuestion(event.target.value)}
                                      defaultValue={question}
                            />
                            <small id="questionHelp" className="form-text text-muted">Provide an MCA Question.</small>
                        </div>
                        <div className="col-md-6">
                            <div className="form-group">
                                <label>Option Type</label>
                                <div className="input-group mb-3">
                                    <select className="custom-select" defaultValue="radio"
                                            onChange={(event) => setOptionType(event.target.value)}>
                                        <option value="radio">Radio - Single Selection Type</option>
                                        <option value="checkbox">Checkbox - Multiple Selection Type</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div className="col-md-6">
                            <div className="form-group">
                                <label>Number of Options</label>
                                <div className="input-group mb-3">
                                    <input className="form-control" type="number" name="optionCount"
                                           min="2" max="8" value="4" placeholder="4" required
                                           onChange={(event) => setOptionCount(event.target.value)}
                                           value={optionCount}
                                    />
                                    <div className="input-group-append">
                                        <button className="btn btn-outline-info"
                                                onClick={() => updateOptionCount(+1)}
                                        >+
                                        </button>
                                        <span className="btn btn-outline-danger"
                                              onClick={() => updateOptionCount(-1, options.length - 1)}
                                        >-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {
                            options.map((option, index) =>
                                <div className="col-md-8" key={index}>
                                    <div className="form-group">
                                        <label>Option {index + 1}</label>
                                        <div className="input-group mb-3">
                                            <div className="input-group-prepend">
                                                <div className="input-group-text">
                                                    <input type={optionType === 'checkbox' ? 'checkbox' : 'radio'}
                                                           aria-label="Checkbox for following text input"
                                                    />
                                                </div>
                                            </div>
                                            <input type="text" className="form-control"
                                                   onChange={(event) => updateOption(event, index)}/>
                                        </div>
                                    </div>
                                </div>
                            )
                        }
                    </div>

                    {/*<input type="password" className="form-control"*/}
                    {/*       onChange={(event) => setPassword(event.target.value)}*/}
                    {/*       value={password}*/}
                    {/*/>*/}

                    <div className="form-group col-12">
                        <button type="button" disabled={!valid} className="btn btn-success mr-2 float-right">Save</button>
                        <button type="button" disabled={!valid} className="btn btn-light mx-2 float-right">Add More</button>
                    </div>
                </div>
            </div>
        </div>
    );
};

if (document.getElementById('AddQuestion')) {
    ReactDOM.render(<AddQuestion />, document.querySelector('#AddQuestion'));
}
