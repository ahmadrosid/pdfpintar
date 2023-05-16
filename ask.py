from typing import List, Tuple
from dotenv import load_dotenv
load_dotenv()

from langchain.embeddings.openai import OpenAIEmbeddings
from langchain.vectorstores.pgvector import PGVector, DistanceStrategy
from langchain.docstore.document import Document
from langchain.document_loaders import PyPDFLoader
from langchain.chains.question_answering import load_qa_chain
from langchain.chat_models import ChatOpenAI

embeddings = OpenAIEmbeddings()

import os
CONNECTION_STRING = PGVector.connection_string_from_db_params(
    driver=os.environ.get("PGVECTOR_DRIVER", "psycopg2"),
    host=os.environ.get("DB_HOST", "localhost"),
    port=int(os.environ.get("DB_PORT", "5432")),
    database=os.environ.get("DB_DATABASE", "postgres"),
    user=os.environ.get("DB_USERNAME", "postgres"),
    password=os.environ.get("DB_PASSWORD", ""),
)

store = PGVector(
    connection_string=CONNECTION_STRING, 
    embedding_function=embeddings, 
    collection_name="Learn Makefiles.pdf",
    distance_strategy=DistanceStrategy.COSINE
)

query = "What is Makefile?"
chain = load_qa_chain(ChatOpenAI(temperature=0), chain_type="stuff")
result_docs = store.similarity_search(query)
output = chain({"input_documents": result_docs, "question": query})

metadatas = []
for doc in result_docs:
    metadatas.append(doc.metadata['page'])

print(output['output_text'])
print(metadatas)
