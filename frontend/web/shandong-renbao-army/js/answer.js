(function() {
    const myQuestions = [
      {
        question: "1.2019年10月1日是中华人民共和国成立多少周年？。",
        answers: {
          A: "60周年",
          B: "70周年",
          C: "80周年",
          D: "90周年"
        },
        correctAnswer: "D"
      },
      {
        question: "中国人保是新中国第一家保险公司，那今年是成立多少周年？",
        answers: {
          A: "50周年",
          B: "60周年",
          C: "70周年",
          D: "80周年"
        },
        correctAnswer: "A"
      },
      {
        question: "国庆大阅兵举行时，哪个时刻最让你激情澎湃、感动自豪？。",
        answers: {
          A: "英勇铿锵的陆军方队",
          B: "翱翔苍穹的雄鹰机群",
          C: "翱翔苍穹的雄鹰机群",
          D: "乘风破浪的军舰潜艇"
        },
        correctAnswer: "A"
      },
      {
        question: "下列哪款武器，你觉得其能够代表现代中国辉煌的军事成就？",
        answers: {
          A: "歼20隐形战斗机",
          B: "99G型第三代主战坦克",
          C: "003型国产航空母舰",
          D: "东风31AG导弹"
        },
        correctAnswer: "A"
      },
      {
        question: "如果要你选择一种冒险方式，你会选择？",
        answers: {
          A: "空中跳伞",
          B: "导弹打靶",
          C: "丛林穿越",
          D: "冲浪深潜"
        },
        correctAnswer: "A,",
      },
      {
        question: "下面颜色中你更喜欢哪个？",
        answers: {
          A: "绿色",
          B: "深蓝",
          C: "浅蓝",
          D: "红火"
        },
        correctAnswer: "A,",
      }

    ];

    function buildQuiz() {
      // we'll need a place to store the HTML output
      const output = [];

      // for each question...
      myQuestions.forEach((currentQuestion, questionNumber) => {
        // we'll want to store the list of answer choices
        const answers = [];

        // and for each available answer...
        for (letter in currentQuestion.answers) {
          // ...add an HTML radio button
          answers.push(
            `<label>
               <input type="radio" name="question${questionNumber}" value="${letter}">
                ${letter} :
                ${currentQuestion.answers[letter]}
             </label>`
          );
        }

        // add this question and its answers to the output
        output.push(
          `<div class="slide">
             <div class="question"> ${currentQuestion.question} </div>
             <div class="answers"> ${answers.join("")} </div>
           </div>`
        );
      });

      // finally combine our output list into one string of HTML and put it on the page
      quizContainer.innerHTML = output.join("");
    }

    function showResults() {
      // gather answer containers from our quiz
      const answerContainers = quizContainer.querySelectorAll(".answers");

      // keep track of user's answers
      let numCorrect = 0;

      // for each question...
      myQuestions.forEach((currentQuestion, questionNumber) => {
        // find selected answer
        const answerContainer = answerContainers[questionNumber];
        const selector = `input[name=question${questionNumber}]:checked`;
        const userAnswer = (answerContainer.querySelector(selector) || {}).value;

        // if answer is correct
        if (userAnswer === currentQuestion.correctAnswer) {
          // add to the number of correct answers
          numCorrect++;

          // color the answers green
          answerContainers[questionNumber].style.color = "lightgreen";
        } else {
          // if answer is wrong or blank
          // color the answers red
          answerContainers[questionNumber].style.color = "red";
        }
      });

      // show number of correct answers out of total
      resultsContainer.innerHTML = `你答对了${myQuestions.length}中的${numCorrect}`;
    }

    function showSlide(n) {
      slides[currentSlide].classList.remove("active-slide");
      slides[n].classList.add("active-slide");
      currentSlide = n;
      
      if (currentSlide === 0) {
        previousButton.style.display = "none";
      } else {
        previousButton.style.display = "inline-block";
      }
      
      if (currentSlide === slides.length - 1) {
        nextButton.style.display = "none";
        submitButton.style.display = "inline-block";
      } else {
        nextButton.style.display = "inline-block";
        submitButton.style.display = "none";
      }
    }

    function showNextSlide() {
      showSlide(currentSlide + 1);
    }

    function showPreviousSlide() {
      showSlide(currentSlide - 1);
    }

    const quizContainer = document.getElementById("quiz");
    const resultsContainer = document.getElementById("results");
    const submitButton = document.getElementById("submit");

    // display quiz right away
    buildQuiz();

    const previousButton = document.getElementById("previous");
    const nextButton = document.getElementById("next");
    const slides = document.querySelectorAll(".slide");
    let currentSlide = 0;

    showSlide(0);

    // on submit, show results
    submitButton.addEventListener("click", showResults);
    previousButton.addEventListener("click", showPreviousSlide);
    nextButton.addEventListener("click", showNextSlide);
  })();
