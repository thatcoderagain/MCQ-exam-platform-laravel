import React, {Fragment, useEffect, useState} from 'react';
import ReactDOM from 'react-dom';
import RawHTML from "./RawHTML";

export const AddQuestion = (props) => {
    const optionObject = {title: '', media: '', correctness: false};
    const [question, setQuestion] = useState('');
    const [optionType, setOptionType] = useState('radio');
    const [optionCount, setOptionCount] = useState(4);
    const [options, setOptions] = useState([
        optionObject, optionObject, optionObject, optionObject
    ]);
    const [saveMode, setSaveMode] = useState('');
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

    const updateOptionCount = (number, index = null) => {
        event.preventDefault();
        if ((number < 0 && optionCount > 2) || (number > 0 && optionCount < 8)) {
            setOptionCount(optionCount + number);
            if (number < 0) {
                setOptions([...options.slice(0, index), ...options.slice(index + 1)]);
            } else {
                setOptions([...options, ...[{...optionObject}]])
            }
        }
    };

    const updateOption = (event, index) => {
        let value = event.target.value;
        setOptions([...options.slice(0, index), {...options[index], title: value}, ...options.slice(index + 1)]);
    };

    const updateCorrectness = (event, index) => {
        let value = event.target.checked;
        if (optionType === 'radio') {
            setOptions(options.map((option, i) => {
                return {...option, correctness: index === i ? value : false};
            }, index))
        } else {
            setOptions([...options.slice(0, index), {...options[index], correctness: value}, ...options.slice(index + 1)]);
        }
    };

    const hasDuplicates = (array) => {
        return (new Set(array)).size !== array.length;
    };

    const submit = (type) => {
        setSaveMode(type);
    };

    useEffect(() => {
        if (valid && (saveMode === 'finish' || saveMode === 'add_more')) {
            $('#AddQuestionForm').closest("form").submit();
        }
    }, [saveMode]);

    useEffect(() => {
        let valid = true;
        if (question.length < 10) {
            setError('Questions should be at least 10 characters long.');
            valid = false;
        } else if (optionType !== 'radio' && optionType !== 'checkbox') {
            setError('Options type can only be either Radio or Checkbox.');
            valid = false;
        } else if (!Number.isInteger(optionCount) || (optionCount < 2 && optionCount > 8)) {
            setError('Option count must be between 5 to 180 minutes.');
            valid = false;
        }
        else if (options.filter((i) => !i.title.trim().length).length) {
            setError('No option can be empty.');
            valid = false;
        } else if (hasDuplicates(options.map((i) => i.title.trim()))) {
            setError('Options can\'t have duplicate values.');
            valid = false;
        } else if (options.filter((i) => i.correctness).length === 0) {
            setError('At least one option should be correct and checked.');
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
                            showError
                                ? <div className="col-12"><div className="alert alert-danger" role="alert">{error}</div></div>
                                : <RawHTML className="col-12">{props.children}</RawHTML>
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
                                    <select className="custom-select" name="optionType" defaultValue="radio"
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
                                <div className="col-md-6 mr-4" key={index}>
                                    <div className="form-group">
                                        <label>Option {index + 1}</label>
                                        <div className="input-group mb-3">
                                            <div className="input-group-prepend">
                                                <div className="input-group-text">
                                                    <input type={optionType === 'checkbox' ? 'checkbox' : 'radio'}
                                                           name="correctness[]"
                                                           value={index} defaultChecked={option.correctness}
                                                           onChange={(event) => updateCorrectness(event, index)}
                                                    />
                                                </div>
                                            </div>
                                            <input type="text" className="form-control" name="options[]" value={option.title}
                                                   onChange={(event) => updateOption(event, index)}/>
                                        </div>
                                    </div>
                                </div>
                            )
                        }
                    </div>

                    <div className="form-group col-12">
                        <div className="form-group col-12">
                            <input type="hidden" name="saveMode" value={saveMode}/>
                            {
                                valid
                                    ? <Fragment>
                                        <button type="button" onClick={() => submit('finish')} className="btn btn-success mr-2 float-right">Save & Finish</button>
                                        <button type="button" onClick={() => submit('add_more')} className="btn btn-success mr-2 float-right">Add More</button>
                                    </Fragment>
                                    : <Fragment>
                                        <button type="button" onClick={validate} className="btn btn-outline-success mr-2 float-right">Save & Finish</button>
                                        <button type="button" onClick={validate} className="btn btn-outline-success mr-2 float-right">Add More</button>
                                    </Fragment>
                            }
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

if (document.getElementById('AddQuestion')) {
    ReactDOM.render(<AddQuestion>{document.querySelector('#AddQuestion').innerHTML}</AddQuestion>,
        document.querySelector('#AddQuestion'));
}
