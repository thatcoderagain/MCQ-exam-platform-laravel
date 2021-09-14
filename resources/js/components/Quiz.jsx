import React, {Fragment, useEffect, useState} from 'react';
import ReactDOM from 'react-dom';
import moment from 'moment';
import RawHTML from "./RawHTML";

export const Quiz = (props) => {
    const parsedQuiz = JSON.parse(props.quiz);
    const parsedQuestion = JSON.parse(props.question);
    const parsedAnswer = JSON.parse(props.answer);
    const parsedQuestions = JSON.parse(props.questions);
    const parsedQuestionsStatus = JSON.parse(props.questionsStatus);

    const [quiz, setQuiz] = useState(parsedQuiz);
    const [question, setQuestion] = useState(parsedQuestion);
    const [questions, setQuestions] = useState(parsedQuestions);
    const [answer, setAnswer] = useState(parsedAnswer);
    const [seenQuestions, setSeenQuestions] = useState(parsedQuestionsStatus.seen);
    const [attemptedQuestions, setAttemptedQuestions] = useState(parsedQuestionsStatus.attempted);
    const [markedQuestions, setMarkedForReviewQuestions] = useState(parsedQuestionsStatus.marked);
    const [activeQuestionNumber, setActiveQuestionNumber] = useState(quiz.activeQuestionNumber);

    const [options, setOptions] = useState([]);
    const [optionType, setOptionType] = useState(parsedQuestion.option_type);
    const [submitMode, setSubmitMode] = useState('submit');
    const [error, setError] = useState('');
    const [showError, setShowError] = useState(false);

    const timeLeft = moment.duration(moment(quiz.endTime).subtract(moment()) - 1000, "milliseconds");
    const [time, setTime] = useState(timeLeft);

    const timer = setTimeout(() => {
        setTime(moment.duration(time - 1000, "milliseconds"));
    }, 1000);

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
                        <div className="w-25">
                            {
                                questions.map((question, index) => {
                                    const url = '/test/quiz/'+quiz.id+'/question/'+(index+1);
                                    let buttonColor;
                                    if (activeQuestionNumber === index) {
                                        buttonColor = 'btn-info';
                                    } else if (markedQuestions.includes(question.id)) {
                                        buttonColor = 'btn-danger';
                                    } else if (attemptedQuestions.includes(question.id)) {
                                        buttonColor = 'btn-success';
                                    } else if (seenQuestions.includes(question.id)) {
                                        buttonColor = 'btn-warning';
                                    } else {
                                         buttonColor = 'btn-secondary';
                                    }
                                    return (<a href={url} key={index} role="button" className={`btn btn-sm m-1 ${buttonColor}`}>{index+1}</a>);
                                })
                            }
                        </div>
                    </div>
                </div>
            </div>
            <div className="col-8">
                <div className="card">
                    <div className="card-header">
                        <h3 className="float-right">Time left: { time.hours()}h {time.minutes()}m {time.seconds()}s</h3>
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
                                <textarea className="form-control" rows="3" readOnly={true} value={question.title}/>
                                <input type="hidden" name="questionId" value={question.id}/>
                                <input type="hidden" name="activeQuestionNumber" value={activeQuestionNumber + 1}/>
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
                                                               defaultChecked={answer.includes(`${index}`)}
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
                                <input type="hidden" name="submitMode" value={submitMode}/>
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
