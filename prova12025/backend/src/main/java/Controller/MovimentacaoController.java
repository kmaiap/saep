package com.saep.backend.Controller;

import java.util.List;


import org.springframework.http.ResponseEntity;

import com.saep.backend.Model.Movimentacao;
import com.saep.backend.Repository.MovimentacaoRepository;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;


@RestController
@RequestMapping("/api/movimentacoes")
public class MovimentacaoController {
    
    private final MovimentacaoRepository movimentacaoRepository;

    MovimentacaoController(MovimentacaoRepository movimentacaoRepository) {
        this.movimentacaoRepository = movimentacaoRepository;
    }

    //Método para buscar todas as movimentações
    @GetMapping()
    public List<Movimentacao> buscarMovimentacoes() {
        return movimentacaoRepository.findAll();
    }
        
    @PostMapping
    public Movimentacao registrarMovimentacao(@RequestBody Movimentacao movimentacao) {
        return movimentacaoRepository.save(movimentacao);
    }

    // @PostMapping()
    // public ResponseEntity<?> registrar2(@RequestBody Movimentacao movimentacao) {
    //     //TODO: process POST request
    //     try {
    //         movimentacaoRepository.save(movimentacao);
    //     } catch (Exception e) {
    //         return ResponseEntity.badRequest().body(e.getMessage());
    //     }
    //     return null;
    // }
}
