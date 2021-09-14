import React, {Fragment, useEffect, useState} from 'react';
import ReactDOM from 'react-dom';
import RawHTML from "./RawHTML";

export const Quiz = (props) => {
    const parsedQuestion = JSON.parse(props.question);
    const parsedQuestions = JSON.parse(props.questions);
    const paresedseenQuestions = JSON.parse(props.seenQuestions);
    const paresedattemptedQuestions = JSON.parse(props.attemptedQuestions);
    const paresedmarkedForReviewQuestions = JSON.parse(props.markedForReviewQuestions);

    const [quizId, setQuizId] = useState(parseInt(props.quizId));
    const [question, setQuestion] = useState(parsedQuestion);
    const [questions, setQuestions] = useState(parsedQuestions);
    const [seenQuestions, setSeenQuestions] = useState(paresedseenQuestions);
    const [attemptedQuestions, setAttemptedQuestions] = useState(paresedattemptedQuestions);
    const [markedForReviewQuestions, setMarkedForReviewQuestions] = useState(paresedmarkedForReviewQuestions);
    const [selectedQuestion, setSelectedQuestion] = useState(parseInt(props.selectedQuestion));

    const [options, setOptions] = useState([]);
    const [optionType, setOptionType] = useState(parsedQuestion.option_type);
    const [error, setError] = useState('');
    const [showError, setShowError] = useState(false);

    const updateCorrectness = (event, index) => {
        if (optionType === 'radio') {
            setOptions(index);
        } else {
            setOptions([...options, index]);
        }
    };

    return (
        <Fragment>
            <div className="col-4">
                <div className="card">
                    <div className="card-header">
                        <h3>Dashboard</h3>
                    </div>
                    <div className="card-body">
                        <div className="col">
                            {
                                questions.map((question, index) => {
                                    let buttonColor;
                                    if (selectedQuestion === index) {
                                        buttonColor = 'btn-primary';
                                    } else if (markedForReviewQuestions.includes(question.id)) {
                                        buttonColor = 'btn-danger';
                                    } else if (attemptedQuestions.includes(question.id)) {
                                        buttonColor = 'btn-success';
                                    } else if (seenQuestions.includes(question.id)) {
                                        buttonColor = 'btn-warning';
                                    } else {
                                         buttonColor = 'btn-secondary';
                                    }
                                    let url = '/test/quiz/'+quizId+'/question/'+(index+1);
                                    return <a href={url}
                                        key={index} type="button" className={"btn btn-sm m-1 "+buttonColor}>{index+1}</a>
                                })
                            }
                        </div>
                    </div>
                </div>
            </div>
            <div className="col-8">
                <div className="card">
                    <div className="card-header">
                        <h3 className="float-right">Time left: 95min</h3>
                    </div>

                    <div className="card-body">
                        <div className="row">
                            {
                                showError
                                    ? <div className="col-12"><div className="alert alert-danger" role="alert">{error}</div></div>
                                    : <RawHTML className="col-12">{props.children}</RawHTML>
                            }
                            <div className="form-group col-12">
                                <label>Question: </label>
                                <textarea className="form-control" rows="3" readOnly={true}
                                          value={question.title}
                                          onChange={(event) => setQuestion(event.target.value)}
                                />
                                <input type="hidden" name="questionId" value={question.id}/>
                            </div>
                            {
                                question.options.map((option, index) =>
                                    <div className="col-md-8 mr-4" key={index}>
                                        <div className="form-group">
                                            <div className="input-group mb-3">
                                                <div className="input-group-prepend">
                                                    <div className="input-group-text">
                                                        <input type={question.option_type === 'checkbox' ? 'checkbox' : 'radio'}
                                                               name="correctness[]"
                                                               value={index}
                                                               onChange={(event) => updateCorrectness(event, index)}
                                                        />
                                                    </div>
                                                </div>
                                                <span className="form-control">{option.title}</span>
                                            </div>
                                        </div>
                                    </div>
                                )
                            }
                        </div>

                        <div className="form-group col-12">
                            <div className="form-group col-12">
                                <button disabled={options.length < 1} type="submit" className="btn btn-primary mr-2 float-right">Proceed</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Fragment>
    );
};

const element = document.getElementById('Quiz');
if (element) {
    const props = Object.assign({}, element.dataset);
    ReactDOM.render(<Quiz {...props}>{document.querySelector('#Quiz').innerHTML}</Quiz>, element);
}
