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

    const [options, setOptions] = useState(parsedAnswer);
    const [optionType, setOptionType] = useState(parsedQuestion.option_type);
    const [submitMode, setSubmitMode] = useState('');
    const [error, setError] = useState('');
    const [showError, setShowError] = useState(false);

    const [finishTest, setFinishTest] = useState(false);

    const timeLeft = moment.duration(moment(quiz.endTime).subtract(moment()) - 1000, "milliseconds");
    const [time, setTime] = useState(timeLeft);

    const timer = setTimeout(() => {
        setTime(moment.duration(time - 1000, "milliseconds"));
    }, 1000);

    const clearOptions = () => {
        $('.option-input').prop('checked', false);
        setOptions([]);
        setAnswer([]);
        setSubmitMode('clear');
    };

    const updateCorrectness = (event, index) => {
        let updatedValue;
        if (optionType === 'radio') {
            updatedValue = [index];
        } else {
            if (event.target.checked) {
                updatedValue = [...options, `${index}`.toString()];
            } else {
                updatedValue = options.filter((i) => parseInt(i) !== index);
            }
        }
        setOptions([...updatedValue]);
        setAnswer([...updatedValue]);
    };

    const submit = (type) => {
        setSubmitMode(type);
    };


    const submitTest = () => {
        if (finishTest) {
            $('#TestAttemptForm')
                .closest("form")
                .prop('action', `/test/quiz/${quiz.id}/submit`)
                .submit();
        } else {
            setFinishTest(true);
            setTimeout(() => {
                setFinishTest(false);
            }, 3000);
        }
    };

    useEffect(() => {
        if (submitMode === 'submit' || submitMode === 'clear' || submitMode === 'mark') {
            $('#TestAttemptForm')
                .closest("form")
                .submit();
        }
    }, [submitMode]);


    return (
        <div className="row">
            <div className="col-lg-3 col-md-4 col-sm-12 mb-4">
                <div className="card">
                    <div className="card-header">
                        <h2 className="float-left">Questions</h2>
                        <button className={(!finishTest ? 'btn-primary' : 'btn-danger') + ' btn float-right'} type="button"
                                onClick={submitTest}
                        >{!finishTest ? 'Finish Test' : 'Confirm ?'}</button>
                    </div>
                    <div className="card-body">
                        <div className="container-fluid">
                            <div className="row align-items-center">
                            {
                                questions.map((question, index) => {
                                    const url = '/test/quiz/'+quiz.id+'/question/'+(index+1);
                                    let buttonColor;
                                    if (activeQuestionNumber === index) {
                                        buttonColor = 'btn-primary';
                                    } else if (markedQuestions.includes(`${question.id}`) || markedQuestions.includes(question.id)) {
                                        buttonColor = 'btn-danger';
                                    } else if (attemptedQuestions.includes(`${question.id}`) || attemptedQuestions.includes(question.id)) {
                                        buttonColor = 'btn-success';
                                    } else if (seenQuestions.includes(`${question.id}`) || seenQuestions.includes(question.id)) {
                                        buttonColor = 'btn-warning';
                                    } else {
                                         buttonColor = 'btn-secondary';
                                    }
                                    return <span className="col-lg-3 col-md-6 col-sm-12 my-2">
                                        <a href={url} key={index} role="button" className={`btn-block btn btn-sm ${buttonColor}`}>{index+1}</a>
                                    </span>
                                })
                            }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div className="col-lg-9 col-md-8 col-sm-12">
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
                                <label>Question: {quiz.activeQuestionNumber + 1} </label>
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
                                                               key={index} value={index} name="correctness[]" className="option-input"
                                                               defaultChecked={answer.includes(`${index}`)}
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
                                <button type="button" className="btn btn-outline-success mr-2 float-right"
                                        onClick={clearOptions}>Clear</button>
                                <button type="submit" className="btn btn-outline-danger mr-2 float-right"
                                        disabled={options.length < 1} onClick={() => submit('mark')}>Mark for review</button>
                                <button type="submit" className="btn btn-outline-primary mr-2 float-right"
                                        disabled={options.length < 1} onClick={() => submit('submit')}>Proceed</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

const element = document.getElementById('Quiz');
if (element) {
    const props = Object.assign({}, element.dataset);
    ReactDOM.render(<Quiz {...props}>{document.querySelector('#Quiz').innerHTML}</Quiz>, element);
}
