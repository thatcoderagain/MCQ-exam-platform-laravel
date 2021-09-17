import React, {useEffect, useState} from 'react';
import ReactDOM from 'react-dom';
import { registerables, Chart } from 'chart.js';

// Register Chart.js
Chart.register(...registerables);

const backgroundColor = [
        'rgba(75, 192, 192, 0.2)',
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
    ],
    borderColor = [
        'rgba(75, 192, 192, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)'
    ];

export const TestResult = (props) => {

    const [total, setTotal] = useState(props.total);
    const [correct, setCorrect] = useState(props.correct);
    const [incorrect, setIncorrect] = useState(props.incorrect);
    const [unattended, setUnattended] = useState(props.unattended);
    const percentage = (parseFloat(correct/total)*100).toFixed(2);
    const passed = percentage >= 50;

    useEffect(() => {
        const reportChart = $('#myChart');
        const theReportChart = new Chart( reportChart, {
            type: 'pie',
            data: {
                labels: ['Correct', 'Incorrect', 'Unattended'],
                datasets: [{
                    label: 'Report',
                    data: [correct, incorrect, unattended],
                    backgroundColor,
                    borderColor,
                    borderWidth: 2
                }]
            },
        });

        // when component unmounts
        return () => {
            theReportChart.destroy();
        }
    }, []);


    return (
        <div className="col">
            <div className="row">
                <div className="col-md-7 col-sm-12">
                    <div className="jumbotron">
                        <h1 className="display-4">Woo-hoo! Your test results are here</h1>
                        <p className="lead">{passed ? 'Congratulations!' : 'Sorry!'} You have {passed ? 'passed' : 'failed'} the test. </p>
                        <hr className="my-4"/>
                        <div className="row">
                            <div className="col-6">
                                <h4><span className="badge badge-pill badge-success m-2">{correct}</span> Correct</h4>
                                <h4><span className="badge badge-pill badge-danger m-2">{incorrect}</span> Incorrect</h4>
                                <h4><span className="badge badge-pill badge-info m-2">{unattended}</span> Unattended</h4>
                            </div>
                            <div className="col-6">
                                <h1 className={ (passed ? 'text-success' : 'text-danger') + " mt-4 pt-4 ml-4 pl-4  border-left"}>{ percentage }% <span className={(passed ? 'badge-success' : 'badge-danger') +" badge badge-pill m-2"}>{passed ? 'Passed' : 'Failed'}</span></h1>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="col-md-5 col-sm-1 border2">
                    <div className="chartBox border">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    );
};

const element = document.getElementById('TestResult');
if (element) {
    const props = Object.assign({}, element.dataset);
    ReactDOM.render(<TestResult {...props}>{document.querySelector('#TestResult').innerHTML}</TestResult>, element);
}
