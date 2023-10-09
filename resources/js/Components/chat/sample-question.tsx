type SampleQuestionsProps = {
    handlePromptButton: (question: string) => void;
};
export function SampleQuestions({ handlePromptButton }: SampleQuestionsProps) {
    const sampleQuestions: [string, string][] = [
        ["Give me the summary", "of the document in list!"],
        ["What are the key takeaways", "from the document?"],
    ];

    return (
        <>
            <div className="flex w-full h-full flex-col justify-end px-12 pb-4">
                <div className="grid grid-cols-2 gap-4">
                    {sampleQuestions.map((q, idx) => (
                        <button
                            key={idx}
                            onClick={() => handlePromptButton(q.join(" "))}
                            className="col border rounded-lg p-4 hover:bg-gray-300 flex items-center text-left"
                        >
                            <div className="flex-1">
                                <p className="font-bold">{q[0]}</p>
                                <p>{q[1]}</p>
                            </div>
                        </button>
                    ))}
                </div>
            </div>
        </>
    );
}
