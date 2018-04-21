# Contributing to the project

First off all, thanks for taking the time to contribute to the project!

The following is a set of guidelines for contributing to the project. These are mostly guidelines, not rules. Use your best judgment, and feel free to propose changes to this document in a pull request.

## Table of contents

* [Code of Conduct](#code-of-conduct)

* [I just have a question!](#i-just-have-a-question)

* [What should I know before I get started?](#what-should-i-know-before-i-get-started)
  * [Atom Design Decisions](#design-decisions)

* [How can I contribute?](#how-can-i-contribute)
  * [Reporting bugs](#reporting-bugs)
  * [Suggesting enhancements](#suggesting-enhancements)
  * [Your first code contribution](#your-first-code-contribution)
  * [Pull requests](#pull-requests)

* [Styleguides](#styleguides)
  * [Git commit messages](#git-commit-messages)

* [Additional notes](#additional-notes)
  * [Issues and pull requests labels](#issues-and-pull-requests-labels)

## Code of Conduct

This project and everyone participating in it is governed by the [Code of Conduct of the project](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code. Please report unacceptable behavior at [info@robtopgames.pe.hu](mailto:info@robtopgames.pe.hu).

## I just have a question!

> **Note:** Please do not file an issue to ask a question. You will get faster results by using the resources below.

We have an official message board with a detailed FAQ and where the community chimes in with helpful advice if you have questions.

* [Discuss, the official Atom and Electron message board](https://discuss.atom.io)
* [Atom FAQ](https://discuss.atom.io/c/faq)

If chat is more your style, you can join the Atom and Electron Slack team:

* [Join our Discord server](https://discord.gg/WrwvmBd)
    * Use the `#questions` channel for general questions or discussion about our projects.
    * Use the `#non-specified` channel to talk with people.
    * There are many other channels available, just check the channels list.

## What should I know before I get started?

### Design Decisions

When we make a significant decision in how we maintain the project and what we can or cannot support, we will document it in the [atom/design-decisions repository](https://github.com/atom/design-decisions). If you have a question around how we do things, check to see if it is documented there. If it is *not* documented there, please open a new topic on [Discuss, the official Atom message board](https://discuss.atom.io) and ask your question.

## How can I contribute?

### Reporting bugs

This section guides you through submitting a bug report for this project. Following these guidelines helps maintainers and the community understand your report, reproduce the behavior, and find related reports.

Before creating bug reports, please check [this list](#before-submitting-a-bug-report) as you might find out that you do not need to create one. When you are creating a bug report, please [include as many details as possible](#how-do-i-submit-a-good-bug-report). Fill out [the required template](ISSUE_TEMPLATE.md), the information it asks for helps us to resolve issues faster.

> **Note:** If you find a **Closed** issue that seems like it is the same thing that you are experiencing, open a new issue and include a link to the original issue in the body of your new one.

#### Before submitting a bug report

* **Check the [FAQs on the forum](https://discuss.atom.io/c/faq)** for a list of common questions and problems.
* **Check that you have [the latest version](https://github.com/DonAlex0/GMDPrivateServer)**.
* **Check if all the steps have been followed at [the setup gudie](https://github.com/DonAlex0/GMDPrivateServer#setup)**.
* **Perform a [search](https://github.com/DonAlex0/GMDPrivateServer/issues?q=is%3Aopen)** to see if the problem has already been reported. If it has **and the issue is still open**, add a comment to the existing issue instead of opening a new one.

#### How do I submit a (good) bug report?

Bugs are tracked as [GitHub issues](https://guides.github.com/features/issues/). Provide the following information by filling in [the issues template](ISSUE_TEMPLATE.md).

Explain the problem and include additional details to help maintainers reproduce the problem:

* **Use a clear and descriptive title** for the issue to identify the problem.
* **Describe the exact steps which reproduce the problem** in as many details as possible. When listing steps, **don't just say what you did, but explain how you did it**.
* **Provide specific examples to demonstrate the steps**. Include links to files or GitHub projects, or copy/pasteable snippets, which you use in those examples. If you are providing snippets in the issue, use [Markdown code blocks](https://help.github.com/articles/markdown-basics/#multiple-lines).
* **Describe the behavior you observed after following the steps** and point out what exactly is the problem with that behavior.
* **Explain which behavior you expected to see instead and why**.
* **Include screenshots and animated GIFs** which show you following the described steps and clearly demonstrate the problem. You can use [this tool](https://www.cockos.com/licecap/) to record GIFs on macOS and Windows, and [this tool](https://github.com/colinkeenan/silentcast) or [this tool](https://github.com/GNOME/byzanz) on Linux.
* **If the problem was not triggered by a specific action**, describe what you were doing before the problem happened and share more information using the guidelines below.

Provide more context by answering these questions:

* **Did the problem start happening recently** (e.g. after updating to a new version) or was this always a problem?
* If the problem started happening recently, **can you reproduce the problem in an older version?** What is the most recent version in which the problem does not happen? You can download older versions from [the releases page](https://github.com/atom/atom/releases).
* **Can you reliably reproduce the issue?** If not, provide details about how often the problem happens and under which conditions it normally happens.

Include details about your configuration and environment:

* **What is the name and version of the OS you are using?**
* **Are you running Geometry Dash in a virtual machine?** If so, which VM software are you using and which operating systems and versions are used for the host and the guest?
* **Which keyboard layout are you using?** Are you using a US layout or some other layout?

### Suggesting enhancements

This section guides you through submitting an enhancement suggestion for this project, including completely new features and minor improvements to existing functionality. Following these guidelines helps maintainers and the community understand your suggestion and find related suggestions.

Before creating enhancement suggestions, please check [this list](#before-submitting-an-enhancement-suggestion) as you might find out that you do not need to create one. When you are creating an enhancement suggestion, please [include as many details as possible](#how-do-i-submit-a-good-enhancement-suggestion). Fill in [the template](ISSUE_TEMPLATE.md), including the steps that you imagine you would take if the feature you are requesting existed.

#### Before submitting an enhancement suggestion

* **Check that you have [the latest version](https://github.com/DonAlex0/GMDPrivateServer)**.
* **Perform a [cursory search](https://github.com/DonAlex0/GMDPrivateServer/issues?utf8=%E2%9C%93&q=)** to see if the enhancement has already been suggested. If it has, add a comment to the existing issue instead of opening a new one.

#### How do I submit a (good) enhancement suggestion?

Enhancement suggestions are tracked as [GitHub issues](https://guides.github.com/features/issues/). Provide the following information:

* **Use a clear and descriptive title** for the issue to identify the suggestion.
* **Provide a step-by-step description of the suggested enhancement** in as many details as possible.
* **Provide specific examples to demonstrate the steps**. Include copy/pasteable snippets which you use in those examples, as [Markdown code blocks](https://help.github.com/articles/markdown-basics/#multiple-lines).
* **Describe the current behavior** and **explain which behavior you expected to see instead** and why.
* **Include screenshots and animated GIFs** which help you demonstrate the steps or point out the part which the suggestion is related to. You can use [this tool](https://www.cockos.com/licecap/) to record GIFs on macOS and Windows, and [this tool](https://github.com/colinkeenan/silentcast) or [this tool](https://github.com/GNOME/byzanz) on Linux.
* **Explain why this enhancement would be useful** to most of the users.
* **List some other private servers where this enhancement exists**.
* **Specify the name and version of the OS you are using**.

### Your first code contribution

Unsure where to begin contributing to this project? You can start by looking through these `Beginner` and `Help wanted` issues:

* [Beginner issues](https://github.com/DonAlex0/GMDPrivateServer/labels/Beginner) - Issues which should only require a few lines of code, and a test or two.
* [Help wanted issues](https://github.com/DonAlex0/GMDPrivateServer/labels/Help%20wanted) - Issues which should be a bit more involved than `Beginner issues`.

Both issue lists are sorted by total number of comments. While not perfect, number of comments is a reasonable proxy for the impact a given change will have.

### Pull requests

* Fill in [the required template](PULL_REQUEST_TEMPLATE.md)
* Do not include issue numbers in the pull request title.
* Include screenshots and animated GIFs in your pull request whenever possible.
* Document new code.
* **Avoid** platform-dependent code.

## Styleguides

### Git commit messages

* Use the past tense ("Added feature" not "Add feature").
* Limit the first line to 72 characters or less.
* Reference issues and pull requests liberally after the first line.

## Additional notes

### Issues and pull request labels

This section lists the labels we use to help us track and manage issues and pull requests.

[GitHub search](https://help.github.com/articles/searching-issues/) makes it easy to use labels for finding groups of issues or pull requests you are interested in. For example, you might be interested in [opened pull requests which have not been reviewed yet](https://github.com/DonAlex0/GMDPrivateServer/labels/Needs%20review). To help you find issues and pull requests, each label is listed with search links for finding open items with that label. We encourage you to read about [other search filters](https://help.github.com/articles/searching-issues/) which will help you write more focused queries.

The labels are loosely grouped by their purpose, but it is not required that every issue have a label from every group or that an issue cannot have more than one label from the same group.

Please open an issue if you have suggestions for new labels.

#### Type of issue and issue state

| Label names | Search links | Descriptions |
| --- | --- | --- |
| `Enhancement` | [Search](https://github.com/DonAlex0/GMDPrivateServer/labels/Enhancement() | Feature requests. |
| `Bug` | [Search](https://github.com/DonAlex0/GMDPrivateServer/labels/Bug) | Confirmed bugs or reports that are very likely to be bugs. |
| `Question` | [Search](https://github.com/DonAlex0/GMDPrivateServer/labels/Question) | Questions more than bug reports or feature requests (e.g. how do I do X). |
| `Feedback` | [Search](https://github.com/DonAlex0/GMDPrivateServer/labels/Feedback) | General feedback more than bug reports or feature requests. |
| `Help wanted` | [Search](https://github.com/DonAlex0/GMDPrivateServer/labels/Help%20wanted) | The project core team would appreciate help from the community in resolving these issues. |
| `Beginner` | [Search](https://github.com/DonAlex0/GMDPrivateServer/labels/Beginner) | Less complex issues which would be good first issues to work on for users who want to contribute to the project. |
| `More information needed` | [Search](https://github.com/DonAlex0/GMDPrivateServer/labels/More%20information%20needed) | More information needs to be collected about these problems or feature requests (e.g. steps to reproduce). |
| `Needs reproduction` | [Search](https://github.com/DonAlex0/GMDPrivateServer/labels/Needs%20reproduction) | Likely bugs, but have not been reliably reproduced. |
| `Blocked` | [Search](search-atom-repo-label-blocked) | Issues blocked on other issues. |
| `Duplicated` | [Search](https://github.com/DonAlex0/GMDPrivateServer/labels/Duplicated) | Issues which are duplicates of other issues. |
| `Won't fix` | [Search](https://github.com/DonAlex0/GMDPrivateServer/labels/Wont%20fix) | The project core team has decided not to fix these issues for now, either because they are working as intended or for some other reason. |
| `Invalid` | [Search](https://github.com/DonAlex0/GMDPrivateServer/labels/Invalid) | Issues which are not valid (e.g. user errors). |

#### Topic categories

| Label names | Search links | Descriptions |
| --- | --- | --- |
| `Documentation` | [search](https://github.com/DonAlex0/GMDPrivateServer/labels/Documentation) | Related to any type of documentation. |
| `Performance` | [search](https://github.com/DonAlex0/GMDPrivateServer/labels/Performance) | Related to performance. |
| `Security` | [search](https://github.com/DonAlex0/GMDPrivateServer/labels/Security) | Related to security. |
| `UI` | [search](https://github.com/DonAlex0/GMDPrivateServer/labels/UI) | Related to visual design. |
| `API` | [search](https://github.com/DonAlex0/GMDPrivateServer/labels/API) | Related to public APIs. |
| `Uncaught exception` | [search](https://github.com/DonAlex0/GMDPrivateServer/labels/Uncaught%20exception) | Issues about uncaught exceptions. |
| `Encoding` | [search](https://github.com/DonAlex0/GMDPrivateServer/labels/Encoding) | Related to character encoding. |
| `Git` | [search](https://github.com/DonAlex0/GMDPrivateServer/labels/Git) | Related to Git functionality (e.g. problems with gitignore files or with showing the correct file status). |

#### Pull requests labels

| Label names | Search links | Descriptions |
| --- | --- | --- |
| `Work in Progress` | [search](https://github.com/DonAlex0/GMDPrivateServer/labels/Work%20in%20Progress) | Pull requests which are still being worked on, more changes will follow. |
| `Needs review` | [search](https://github.com/DonAlex0/GMDPrivateServer/labels/Needs%20review) | Pull requests which need code review, and approval from maintainers or project core team. |
| `Under review` | [search](https://github.com/DonAlex0/GMDPrivateServer/labels/Under%20review) | Pull requests being reviewed by maintainers or project core team. |
| `Requires changes` | [search](https://github.com/DonAlex0/GMDPrivateServer/labels/Requires%20changes) | Pull requests which need to be updated based on review comments and then reviewed again. |
| `Needs testing` | [search](https://github.com/DonAlex0/GMDPrivateServer/labels/Needs%20testing) | Pull requests which need manual testing. |