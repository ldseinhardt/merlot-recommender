package merlot.recommender;

import java.io.*;
import java.util.*;
import org.json.*;
import org.apache.mahout.cf.taste.common.TasteException;
import org.apache.mahout.cf.taste.eval.RecommenderBuilder;
import org.apache.mahout.cf.taste.eval.RecommenderEvaluator;
import org.apache.mahout.cf.taste.impl.eval.AverageAbsoluteDifferenceRecommenderEvaluator;
import org.apache.mahout.cf.taste.impl.eval.RMSRecommenderEvaluator;
import org.apache.mahout.cf.taste.impl.model.file.FileDataModel;
import org.apache.mahout.cf.taste.impl.neighborhood.NearestNUserNeighborhood;
import org.apache.mahout.cf.taste.impl.recommender.GenericUserBasedRecommender;
import org.apache.mahout.cf.taste.impl.similarity.*;
import org.apache.mahout.cf.taste.recommender.*;
import org.apache.mahout.cf.taste.model.DataModel;
import org.apache.mahout.cf.taste.neighborhood.UserNeighborhood;
import org.apache.mahout.cf.taste.similarity.UserSimilarity;

public class MerlotRecommender {
    
    public static void main(String[] args) throws IOException, TasteException {  
        /* id do usuário a qual se deseja gerar recomendações */
        int iduser = 0;
        /* Nome do arquivo de ratings (iduser,idobject,rating) */
        String ratings = ""; 
        /* Realizar Calculos de erros? */
        boolean error = false;
        
        /* Objeto JSON de resposta */
        JSONObject result = new JSONObject();
        
        /*
            Verifica se foi passado dois argumentos: 
            - id de usuário
            - localização do arquivo de ratings
        */
        if (args.length >= 3) {
            iduser = Integer.parseInt(args[0]);
            ratings = args[1];
            error = Boolean.parseBoolean(args[2]);
        } else {
            System.exit(1);
        }
        
        /*
            Define que as mensagens de logs sejam do nível de erro 
            evitando assim, mensagens de notificação
        */
        System.setProperty("org.slf4j.simpleLogger.defaultLogLevel", "error");
        /*
            Em caso de logs de erros, estes devem ser escritos em um arquivo
        */
        System.setProperty("org.slf4j.simpleLogger.logFile", "MerlotRecommender.log");
        
        /* Leitura do arquivo de ratings e geração do modelo */
        FileDataModel model = new FileDataModel(new File(ratings));
        
        /* Construção de um recomendador */
        RecommenderBuilder builder = new RecommenderBuilder() {
            @Override
            public Recommender buildRecommender(DataModel dm) throws TasteException {
                UserSimilarity sim = new PearsonCorrelationSimilarity(dm);
                UserNeighborhood neighborhood = new NearestNUserNeighborhood(10, sim, dm);        
                return new GenericUserBasedRecommender(dm, neighborhood, sim);
            }
    	};        
        
        /*
            Avaliação de erro para o recomendador
            MAE = Erro absoluto médio
            RMS = Raiz do erro quadrático médio
        */
        if (error) {
            RecommenderEvaluator evaluatorMAE = new AverageAbsoluteDifferenceRecommenderEvaluator();
            RecommenderEvaluator evaluatorRMS = new RMSRecommenderEvaluator();
            double scoreMAE = 0, scoreRMS = 0;
            int num = 50;
            for(int i = 0; i < num; i++) {
                scoreMAE += evaluatorMAE.evaluate(builder, null, model, 0.8, 1.0);
                scoreRMS += evaluatorRMS.evaluate(builder, null, model, 0.8, 1.0);            
            }
            scoreMAE /= num;
            scoreRMS /= num;
            result.put("mae", scoreMAE);
            result.put("rms", scoreRMS);
        }
        
        /* Gera as recomendações para o usuário */
        List<RecommendedItem> list = builder.buildRecommender(model).recommend(iduser, 10);
        JSONArray objects = new JSONArray();
        for(RecommendedItem rec : list){
            JSONObject object = new JSONObject();
            object.put("idobject", rec.getItemID()); 
            object.put("value", rec.getValue());
            objects.put(object);
        }       
        result.put("objects", objects);
        System.out.println(result);
    } 
}